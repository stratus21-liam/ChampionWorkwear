<?php

namespace App\Admin;

use App\Model\Order;
use App\Model\OrderItem;
use SilverStripe\Admin\ModelAdmin;

class OrderAdmin extends ModelAdmin
{
    private static $menu_title = 'Orders';

    private static $url_segment = 'orders';

    private static $menu_icon_class = 'font-icon-cart';
    
    private static $menu_priority = '0.7';    

    private static $managed_models = [
        Order::class,
        // OrderItem::class,
    ];
}