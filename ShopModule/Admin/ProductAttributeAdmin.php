<?php

namespace ShopModule\Admin;

use SilverStripe\Admin\ModelAdmin;
use ShopModule\Model\ProductAttribute;
use ShopModule\Model\ProductAttributeOption;

class ProductAttributeAdmin extends ModelAdmin
{
    private static $menu_title = 'Product Attributes';

    private static $url_segment = 'product-attributes';

    private static $menu_icon_class = 'font-icon-list';
    
    private static $menu_priority = '0.7';    

    private static $managed_models = [
        ProductAttribute::class,
        // ProductAttributeOption::class,
    ];
}