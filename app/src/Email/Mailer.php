<?php

namespace App\Email;

use App\Model\Order;
use SilverStripe\Security\Member;
use SilverStripe\SiteConfig\SiteConfig;
use SilverStripe\View\ArrayData;

class Mailer
{
    public static function sendTemplateEmail(
        string $to,
        string $subject,
        string $template,
        array $data = [],
        ?string $replyTo = null
    ) {
        $email = StyledEmail::create($to, $subject);

        if ($replyTo && filter_var($replyTo, FILTER_VALIDATE_EMAIL)) {
            $email->setReplyTo($replyTo);
        }

        $body = ArrayData::create($data)->renderWith($template);

        $email->build($body);

        return $email->send();
    }

    public static function sendGenericEmail(string $to, string $subject, string $body)
    {
        return self::sendTemplateEmail(
            $to,
            $subject,
            'App\Email\GenericEmail',
            [
                'Title' => $subject,
                'Subject' => $subject,
                'Body' => $body,
            ]
        );
    }

    public static function sendOrderSubmittedToCustomer(Order $order)
    {
        $member = $order->Customer();

        if (!$member || !$member->Email) {
            return false;
        }

        $subject = sprintf('Order %s received', $order->OrderNumber);

        return self::sendTemplateEmail(
            $member->Email,
            $subject,
            'App\Email\OrderSubmittedCustomer',
            [
                'Title' => $subject,
                'Subject' => $subject,
                'Order' => $order,
                'Items' => $order->Items(),
                'Customer' => $member,
                'OrderSummaryHTML' => self::renderOrderSummary(
                    $order,
                    $member,
                    false,
                    true,
                    true
                ),
            ]
        );
    }

    public static function sendOrderSubmittedToChampion(Order $order): void
    {
        $siteConfig = SiteConfig::current_site_config();

        if (!$siteConfig || !$siteConfig->hasMethod('OrderNotificationRecipients')) {
            return;
        }

        $recipients = $siteConfig->OrderNotificationRecipients()->filter('Active', 1);
        $customer = $order->Customer();
        $subject = sprintf('New order submitted: %s', $order->OrderNumber);

        foreach ($recipients as $recipient) {
            if (!$recipient->Email) {
                continue;
            }

            self::sendTemplateEmail(
                $recipient->Email,
                $subject,
                'App\Email\OrderSubmittedChampion',
                [
                    'Title' => $subject,
                    'Subject' => $subject,
                    'Order' => $order,
                    'Items' => $order->Items(),
                    'Customer' => $customer,
                    'Recipient' => $recipient,
                    'OrderSummaryHTML' => self::renderOrderSummary(
                        $order,
                        $customer && $customer->exists() ? $customer : null,
                        true,
                        true,
                        true
                    ),
                ],
                $customer && $customer->Email ? $customer->Email : null
            );
        }
    }

    public static function sendOrderApprovalRequestToCustomerAccountAdmins(Order $order): void
    {
        $admins = self::getCustomerAccountAdmins($order);

        if (!$admins->count()) {
            return;
        }

        $subject = sprintf('Order %s requires approval', $order->OrderNumber);

        foreach ($admins as $admin) {
            if (!$admin->Email) {
                continue;
            }

            self::sendTemplateEmail(
                $admin->Email,
                $subject,
                'App\Email\OrderApprovalRequestCustomerAdmin',
                [
                    'Title' => $subject,
                    'Subject' => $subject,
                    'Order' => $order,
                    'Items' => $order->Items(),
                    'Customer' => $order->Customer(),
                    'Admin' => $admin,
                    'OrderSummaryHTML' => self::renderOrderSummary(
                        $order,
                        $order->Customer()->exists() ? $order->Customer() : null,
                        false,
                        true,
                        true
                    ),
                ]
            );
        }
    }

    public static function sendOrderApprovedToCustomer(Order $order)
    {
        $member = $order->Customer();

        if (!$member || !$member->Email) {
            return false;
        }

        $subject = sprintf('Order %s approved', $order->OrderNumber);

        return self::sendTemplateEmail(
            $member->Email,
            $subject,
            'App\Email\OrderApprovedCustomer',
            [
                'Title' => $subject,
                'Subject' => $subject,
                'Order' => $order,
                'Items' => $order->Items(),
                'Customer' => $member,
                'OrderSummaryHTML' => self::renderOrderSummary(
                    $order,
                    $member,
                    false,
                    true,
                    true
                ),
            ]
        );
    }

    public static function sendOrderRejectedToCustomer(Order $order)
    {
        $member = $order->Customer();

        if (!$member || !$member->Email) {
            return false;
        }

        $subject = sprintf('Order %s rejected', $order->OrderNumber);

        return self::sendTemplateEmail(
            $member->Email,
            $subject,
            'App\Email\OrderRejectedCustomer',
            [
                'Title' => $subject,
                'Subject' => $subject,
                'Order' => $order,
                'Items' => $order->Items(),
                'Customer' => $member,
                'OrderSummaryHTML' => self::renderOrderSummary(
                    $order,
                    $member,
                    false,
                    true,
                    true
                ),
            ]
        );
    }

    protected static function getCustomerAccountAdmins(Order $order)
    {
        if (!$order->CustomerAccountID) {
            return Member::get()->filter('ID', 0);
        }

        return Member::get()->filter([
            'CustomerAccountID' => $order->CustomerAccountID,
            'IsAdmin' => 1,
        ]);
    }

    protected static function renderOrderSummary(
        Order $order,
        ?Member $customer,
        bool $showCustomerDetails,
        bool $showDeliveryDetails,
        bool $showItemOptions
    ): string {
        return ArrayData::create([
            'Order' => $order,
            'Items' => $order->Items(),
            'Customer' => $customer,
            'ShowCustomerDetails' => $showCustomerDetails,
            'ShowDeliveryDetails' => $showDeliveryDetails,
            'ShowItemOptions' => $showItemOptions,
        ])->renderWith([
            'Includes\OrderSummaryForEmails',
            'OrderSummaryForEmails',
        ]);
    }
}
