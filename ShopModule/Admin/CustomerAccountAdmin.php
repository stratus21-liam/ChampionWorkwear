<?php

use SilverStripe\Admin\ModelAdmin;
use Symbiote\GridFieldExtensions\GridFieldOrderableRows;
use NewedgeESAModule\Model\Country;
use ShopModule\Model\CustomerAccount;
/**
 * Admin interface to manage and create {@link Subsite} instances.
 *
 * @package subsites
 */
class CustomerAccountAdmin extends ModelAdmin
{
    private static $managed_models = [
        CustomerAccount::class
    ];

    private static $url_segment = 'customer-account';

    private static $menu_title = 'Customer Accounts';

    private static $menu_priority = '0.7';    

    private static $menu_icon_class = 'font-icon-torsos-all';

    public $showImportForm = false;

    private static $tree_class = CustomerAccount::class;

    public function getEditForm($id = null, $fields = null) {

        $form = parent::getEditForm($id, $fields);

        // Instrument specific settings
        if($this->modelClass == CustomerAccount::class) {

            $gridFieldName = $this->sanitiseClassName($this->modelClass);
            $gridField = $form->Fields()->fieldByName($gridFieldName);

            // $config = $gridField->getConfig();

            // $config->addComponent(new GridFieldOrderableRows('SortOrder'));

            // Configure 'Add New' button text
            // $config->removeComponentsByType($config->getComponentByType(GridFieldAddNewButton::class));
            // $config->removeComponentsByType($config->getComponentByType('GridFieldDeleteAction'));

            // $config->addComponent(new GridFieldAddNewInlineButton());
            // $config->addComponent(new GridFieldEditableColumns());
        }       

        return $form;

    } 
}
