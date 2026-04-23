<?php

namespace App\Pages;

use PageController;
use SilverStripe\Security\Security;
use SilverStripe\SiteConfig\SiteConfig;
use ShopModule\Model\Product;

class ProductPageController extends PageController
{
    private static $allowed_actions = [
        'view',
    ];

    private static $url_handlers = [
        '$URLSegment!'      => 'view',
    ];

    /**
     * Event view - the event json data is returned in its own route - app/src/Control/CalendarController.php
     *
     * @param [type] $request
     *
     * @return void
     */
    public function view($request)
    {
        $item = Product::get()
            ->filter('URLSegment', $request->param('URLSegment'))
            ->first();

        if (!$item) {
            return $this->httpError(404);
        }

        $member = Security::getCurrentUser();

        if (!$member) {
            return $this->httpError(404);
        }

        // Admins can see all products
        if ($member->IsAdmin || $member->inGroup('administrators')) {
            return $this->customise([
                'Item' => $item,
            ])->render();
        }

        // Member must belong to the same customer account
        if (
            !$member->CustomerAccountID ||
            !$item->CustomerAccountID ||
            (int)$member->CustomerAccountID !== (int)$item->CustomerAccountID
        ) {
            return $this->httpError(404);
        }

        // Member must have a role and that role must be assigned to the product
        if (!$member->RoleID || !$item->Roles()->byID($member->RoleID)) {
            return $this->httpError(404);
        }

        return $this->customise([
            'Item' => $item,
        ])->render();
    }

}
