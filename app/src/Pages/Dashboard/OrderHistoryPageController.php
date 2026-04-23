<?php

namespace App\Pages;

use App\Extension\SinglePageInstance;
use App\Model\Order;
use Page;
use PageController;
use SilverStripe\Security\Security;


class OrderHistoryPageController extends PageController
{
    private static $allowed_actions = [
        'view',
    ];

    private static $url_handlers = [
        '$OrderNumber!' => 'view',
    ];

    public function init()
    {
        parent::init();
    }

    public function CurrentMember()
    {
        return Security::getCurrentUser();
    }

    public function MyOrders()
    {
        $member = $this->CurrentMember();

        if (!$member) {
            return Order::get()->filter('ID', 0);
        }

        return $member->Orders()->sort('Created', 'DESC');
    }

    public function view($request)
    {
        $member = Security::getCurrentUser();

        if (!$member) {
            return $this->httpError(404);
        }

        $orderNumber = trim((string) $request->param('OrderNumber'));

        if (!$orderNumber) {
            return $this->httpError(404);
        }

        // CMS administrators can view any order
        if ($member->inGroup('administrators')) {
            $item = Order::get()
                ->filter('OrderNumber', $orderNumber)
                ->first();
        } else {
            // Regular users can only ever resolve their own order
            $item = Order::get()
                ->filter([
                    'OrderNumber' => $orderNumber,
                    'CustomerID' => (int) $member->ID,
                ])
                ->first();
        }

        if (!$item) {
            return $this->httpError(404);
        }

        return $this->customise([
            'Item' => $item,
            'Order' => $item,
        ])->render();
    }
}