<?php

namespace App\Pages;

use App\Email\Mailer;
use App\Extension\SinglePageInstance;
use App\Model\Order;
use Page;
use SilverStripe\ORM\DataList;
use SilverStripe\ORM\DB;
use SilverStripe\Security\Member;
use SilverStripe\Security\Security;

class PendingOrdersPage extends Page
{
    private static $singular_name = 'Pending Orders Page';

    private static $icon_class = 'font-icon-clock';

    private static $db = [];

    private static $has_one = [];

    private static $extensions = [
        SinglePageInstance::class
    ];

    public function getCMSFields()
    {
        $fields = parent::getCMSFields();
        return $fields;
    }

    public function CurrentMember()
    {
        return Security::getCurrentUser();
    }

    public function PendingOrders()
    {
        return $this->data()->getPendingOrdersForMember($this->CurrentMember());
    }
    
    public function RejectedOrders()
    {
        return $this->data()->getRejectedOrdersForMember($this->CurrentMember());
    }

    public function canAccessPendingOrders(?Member $member): bool
    {
        if (!$member) {
            return false;
        }

        if ($member->inGroup('administrators')) {
            return true;
        }

        return (bool) $member->IsAdmin;
    }

    public function getPendingOrdersForMember(?Member $member): DataList
    {
        if (!$member) {
            return Order::get()->filter('ID', 0);
        }

        if ($member->inGroup('administrators')) {
            return Order::get()
                ->filter('Status', Order::STATUS_PENDING_APPROVAL)
                ->sort('Created', 'DESC');
        }

        if (!$member->IsAdmin || !$member->CustomerAccountID) {
            return Order::get()->filter('ID', 0);
        }

        return Order::get()
            ->filter([
                'Status' => Order::STATUS_PENDING_APPROVAL,
                'CustomerAccountID' => (int) $member->CustomerAccountID,
            ])
            ->sort('Created', 'DESC');
    }

    public function getRejectedOrdersForMember(?\SilverStripe\Security\Member $member): \SilverStripe\ORM\DataList
    {
        if (!$member) {
            return \App\Model\Order::get()->filter('ID', 0);
        }

        if ($member->inGroup('administrators')) {
            return \App\Model\Order::get()
                ->filter('Status', \App\Model\Order::STATUS_REJECTED)
                ->sort('Created', 'DESC');
        }

        if (!$member->IsAdmin || !$member->CustomerAccountID) {
            return \App\Model\Order::get()->filter('ID', 0);
        }

        return \App\Model\Order::get()
            ->filter([
                'Status' => \App\Model\Order::STATUS_REJECTED,
                'CustomerAccountID' => (int) $member->CustomerAccountID,
            ])
            ->sort('Created', 'DESC');
    }

    public function getPendingOrderForMember(?Member $member, string $orderNumber): ?Order
    {
        $orderNumber = trim($orderNumber);

        if (!$member || !$this->canAccessPendingOrders($member) || $orderNumber === '') {
            return null;
        }

        if ($member->inGroup('administrators')) {
            return Order::get()
                ->filter([
                    'OrderNumber' => $orderNumber,
                    'Status' => Order::STATUS_PENDING_APPROVAL,
                ])
                ->first();
        }

        return Order::get()
            ->filter([
                'OrderNumber' => $orderNumber,
                'Status' => Order::STATUS_PENDING_APPROVAL,
                'CustomerAccountID' => (int) $member->CustomerAccountID,
            ])
            ->first();
    }

    public function approvePendingOrderForMember(?Member $member, string $orderNumber, string $poNumber): ?Order
    {
        $order = $this->getPendingOrderForMember($member, $orderNumber);
        $poNumber = trim($poNumber);

        if (!$order || !$member || $poNumber === '') {
            return null;
        }

        DB::get_conn()->withTransaction(function () use ($order, $member, $poNumber) {
            $existingNotes = trim((string) $order->OrderNotes);
            $auditNote = sprintf(
                'PO Number was updated on %s by %s',
                date('d/m/Y H:i:s'),
                $member->Email ?: 'unknown user'
            );

            $order->Status = Order::STATUS_APPROVED;
            $order->RejectionReason = null;
            $order->RejectedAt = null;
            $order->ApprovedByID = (int) $member->ID;
            $order->ApprovedAt = date('Y-m-d H:i:s');
            $order->PONumber = $poNumber;
            $order->OrderNotes = $existingNotes !== ''
                ? $existingNotes . "\n" . $auditNote
                : $auditNote;
            $order->write();
        });

        try {
            Mailer::sendOrderApprovedToCustomer($order);
            Mailer::sendOrderSubmittedToChampion($order);
        } catch (\Throwable $e) {
            // do not fail approval if emails fail
        }

        return $order;
    }

    public function rejectPendingOrderForMember(?Member $member, string $orderNumber, string $reason): ?Order
    {
        $order = $this->getPendingOrderForMember($member, $orderNumber);
        $reason = trim($reason);

        if (!$order || !$member || $reason === '') {
            return null;
        }

        DB::get_conn()->withTransaction(function () use ($order, $member, $reason) {
            $order->Status = Order::STATUS_REJECTED;
            $order->ApprovedByID = (int) $member->ID;
            $order->RejectedAt = date('Y-m-d H:i:s');
            $order->RejectionReason = $reason;
            $order->write();
        });

        try {
            Mailer::sendOrderRejectedToCustomer($order);
            // Mailer::sendOrderSubmittedToChampion($order); //champion do not need to know about rejection
        } catch (\Throwable $e) {
            // do not fail rejection if emails fail
        }

        return $order;
    }
}
