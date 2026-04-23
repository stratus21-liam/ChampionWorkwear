<?php

namespace App\Pages;

use App\Extension\SinglePageInstance;
use Page;
use PageController;
use SilverStripe\ORM\ArrayList;
use SilverStripe\Security\Security;

class OrderHistoryPage extends Page
{
    private static $singular_name = 'Order History Page';

    private static $icon_class = 'font-icon-list';

    private static $db = [];

    private static $has_one = [];

    private static $extensions = [
        SinglePageInstance::class,
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

    public function MyOrders()
    {
        $member = $this->CurrentMember();

        if (!$member || !$member->ID) {
            return ArrayList::create();
        }

        return $member->Orders()->sort('Created', 'DESC');
    }    
}