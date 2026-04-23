<?php
namespace App\Pages;

use PageController;
use Page;
use App\Extension\SinglePageInstance;
use SilverStripe\Security\Security;
use App\Model\Order;
use SilverStripe\Security\Member;

class DashboardPage extends Page {

    private static $singular_name = "Dashboard Page";

    private static $icon_class = 'font-icon-info-circled';

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

    public function getPendingOrdersCountForMember(): int
    {
        $member = Security::getCurrentUser();

        if (!$member) {
            return 0;
        }

        // CMS admin → all pending
        if ($member->inGroup('administrators')) {
            return Order::get()
                ->filter('Status', Order::STATUS_PENDING_APPROVAL)
                ->count();
        }

        // Customer admin → their account only
        if ($member->IsAdmin && $member->CustomerAccountID) {
            return Order::get()
                ->filter([
                    'Status' => Order::STATUS_PENDING_APPROVAL,
                    'CustomerAccountID' => (int) $member->CustomerAccountID,
                ])
                ->count();
        }

        return 0;
    }    

}

class DashboardPage_Controller extends PageController {

    private static $allowed_actions = [];

    public function init() {
        parent::init();
    }

    public function CurrentMember()
    {
        return Security::getCurrentUser();
    }
}