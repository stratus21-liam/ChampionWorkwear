<?php

namespace ShopModuleTask\Tasks;

use App\Model\Order;
use ShopModule\Model\CustomerAccount;
use ShopModule\Model\Product;
use ShopModule\Model\ProductAttribute;
use SilverStripe\Dev\BuildTask;
use SilverStripe\Security\Member;

class ClearShopModuleDevDataTask extends BuildTask
{
    private const EMAIL_DOMAIN = '@devseed.champion.local';
    private const ORDER_PREFIX = 'DEV-SEED-';
    private const ATTRIBUTE_CODES = [
        'devseed_colour',
        'devseed_size',
        'devseed_personalisation',
        'devseed_finish',
    ];
    private const ACCOUNT_TITLES = [
        'Dev Seed - Apex Facilities',
        'Dev Seed - Northstar Retail',
        'Dev Seed - Meridian Events',
    ];

    private static $segment = 'clear-shopmodule-dev-data';

    protected $title = 'Clear ShopModule dev data';

    protected $description = 'Removes customer accounts, users, products, attributes, and orders created by the ShopModule dev data seed task.';

    public function run($request): void
    {
        $counts = $this->clearData();

        foreach ($counts as $label => $count) {
            echo sprintf('%s deleted: %d<br>', ucfirst($label), $count);
        }
    }

    public function clearData(): array
    {
        $counts = [
            'order items' => 0,
            'orders' => 0,
            'members' => 0,
            'products' => 0,
            'roles' => 0,
            'attribute options' => 0,
            'attributes' => 0,
            'customer accounts' => 0,
        ];

        foreach (Order::get()->filter('OrderNumber:StartsWith', self::ORDER_PREFIX) as $order) {
            foreach ($order->Items() as $item) {
                $item->delete();
                $counts['order items']++;
            }

            $order->delete();
            $counts['orders']++;
        }

        foreach (Member::get()->filter('Email:EndsWith', self::EMAIL_DOMAIN) as $member) {
            $member->delete();
            $counts['members']++;
        }

        foreach (CustomerAccount::get()->filter('Title', self::ACCOUNT_TITLES) as $account) {
            foreach ($account->Products() as $product) {
                $product->delete();
                $counts['products']++;
            }

            foreach ($account->Roles() as $role) {
                $role->delete();
                $counts['roles']++;
            }

            $account->delete();
            $counts['customer accounts']++;
        }

        foreach (Product::get()->filter('SKU:StartsWith', 'DEV-SEED-') as $product) {
            $product->delete();
            $counts['products']++;
        }

        foreach (ProductAttribute::get()->filter('Code', self::ATTRIBUTE_CODES) as $attribute) {
            foreach ($attribute->Options() as $option) {
                $option->delete();
                $counts['attribute options']++;
            }

            $attribute->delete();
            $counts['attributes']++;
        }

        return $counts;
    }
}
