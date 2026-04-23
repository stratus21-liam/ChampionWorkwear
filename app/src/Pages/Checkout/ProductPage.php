<?php
namespace App\Pages;
use PageController;
use Page;
use App\Extension\SinglePageInstance;

class ProductPage extends Page {

    private static $singular_name = "Product Page";

    private static $icon_class = 'font-icon-info-circled';

    // private static $menu_icon_class = 'mdi:face'; //for the left hand menu
    
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

}
