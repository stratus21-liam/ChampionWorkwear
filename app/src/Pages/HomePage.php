<?php
namespace App\Pages;
use PageController;
use Page;

class HomePage extends Page {

    private static $singular_name = "Home Page";

    private static $icon_class = 'font-icon-home';

    // private static $menu_icon_class = 'mdi:face'; //for the left hand menu
    
    private static $db = [];

    private static $has_one = [];

    public function getCMSFields()
    {
        $fields = parent::getCMSFields();
        return $fields;
    }

}
class HomePage_Controller extends PageController {

    private static $allowed_actions = [];

    public function init() {
        parent::init();
        // You can include any CSS or JS required by your project here.
        // See: http://doc.silverstripe.org/framework/en/reference/requirements
    }

}
