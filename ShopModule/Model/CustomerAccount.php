<?php

namespace ShopModule\Model;

use SilverStripe\Forms\CheckboxField;
use SilverStripe\ORM\DataObject;
use SilverStripe\ORM\ValidationResult;
use SilverStripe\Forms\GridField\GridField;
use SilverStripe\Forms\GridField\GridFieldAddNewButton;
use SilverStripe\Forms\GridField\GridFieldConfig_RecordEditor;
use SilverStripe\Forms\GridField\GridFieldAddExistingAutocompleter;
use SilverStripe\Forms\TextField;
use SilverStripe\Security\Security;
use SilverStripe\SiteConfig\SiteConfig;
use Symbiote\GridFieldExtensions\GridFieldAddNewInlineButton;
use Symbiote\GridFieldExtensions\GridFieldEditableColumns;
use Symbiote\GridFieldExtensions\GridFieldOrderableRows;

class CustomerAccount extends DataObject
{
    private static $singular_name = 'Customer Account';

    private static $plural_name = 'Customer Accounts';

    private static $table_name = 'ShopModule_CustomerAccount';

    private static $db = [
        'Title'      => 'Varchar(255)',
        'StoreTitle' => 'Text',
        'Active'     => 'Boolean'
    ];

    private static $summary_fields = [
        'Title' => 'Title',
        'Active.Nice' => 'Active'
    ];

    private static $searchable_fields = [
        'Title'
    ];

    private static $has_many = [
        'Roles' => Role::class,
        'Members' => \SilverStripe\Security\Member::class,
        'Products' => Product::class,
        'Categories' => ProductCategory::class,
    ];

    private static $default_sort = 'Title ASC';

    public function getCMSFields()
    {
        $fields = parent::getCMSFields();

        $fields->removeByName([
            'Members',
            'Roles',
            'Products',
            'Categories',
        ]);

        $fields->addFieldToTab('Root.Main', CheckboxField::create('Active', 'Active'));
        $fields->addFieldToTab('Root.Main', TextField::create('Title', 'Title'));
        $fields->addFieldToTab(
            'Root.Main',
            $info = TextField::create('StoreTitle', 'Store Title')
        );
        $info->setDescription('Shown on homepage to customers');

        if ($this->ID) {
            $rolesConfig = GridFieldConfig_RecordEditor::create();
            $rolesConfig->removeComponentsByType(GridFieldAddExistingAutocompleter::class);

            $rolesGrid = GridField::create(
                'Roles',
                'Roles',
                $this->Roles(),
                $rolesConfig
            );
            $fields->addFieldToTab('Root.Roles', $rolesGrid);

            $productCMSPagination = (int) SiteConfig::current_site_config()->ProductCMSPagination;
            if ($productCMSPagination <= 0) {
                $productCMSPagination = 12;
            }

            $productsConfig = GridFieldConfig_RecordEditor::create($productCMSPagination);
            $productsConfig->addComponent(new GridFieldOrderableRows('Sort'));
            $productsConfig->removeComponentsByType(GridFieldAddExistingAutocompleter::class);

            $productsGrid = GridField::create(
                'Products',
                'Products',
                $this->Products(),
                $productsConfig
            );
            $fields->addFieldToTab('Root.Products', $productsGrid);

            $categoriesConfig = GridFieldConfig_RecordEditor::create();
            $categoriesConfig->addComponent(new GridFieldAddNewInlineButton());
            $categoriesConfig->addComponent(new GridFieldEditableColumns());

            $categoriesGrid = GridField::create(
                'Categories',
                'Categories',
                $this->Categories(),
                $categoriesConfig
            );
            $categoriesGrid->getConfig()->removeComponentsByType(GridFieldAddNewButton::class);
            $categoriesGrid->getConfig()
                ->getComponentByType(GridFieldEditableColumns::class)
                ->setDisplayFields([
                    'Title' => [
                        'title' => 'Title',
                        'callback' => function ($record, $column, $grid) {
                            return TextField::create($column);
                        }
                    ],
                ]);
            $fields->addFieldToTab('Root.Categories', $categoriesGrid);

            $membersConfig = GridFieldConfig_RecordEditor::create();
            $membersConfig->removeComponentsByType(GridFieldAddExistingAutocompleter::class);

            $membersGrid = GridField::create(
                'Members',
                'Users',
                $this->Members(),
                $membersConfig
            );
            $fields->addFieldToTab('Root.Users', $membersGrid);
        }

        return $fields;
    }

    public function getAdminUsers()
    {
        return $this->Members()->filter('IsAdmin', 1);
    }

    public function getSubUsers()
    {
        return $this->Members()->filter('IsAdmin', 0);
    }

    public function getVisibleProducts()
    {
        $member = Security::getCurrentUser();

        if (!$member) {
            return Product::get()->filter('ID', 0);
        }

        if ($member->IsAdmin) {
            return Product::get()->filter([
                'CustomerAccountID' => $member->CustomerAccountID,
                'Active' => 1
            ])->sort('Sort');
        }

        if (!$member->RoleID) {
            return Product::get()->filter('ID', 0);
        }

        return $this->Products()
            ->filter([
                'Active' => 1,
                'Roles.ID' => $member->RoleID,
            ])
            ->sort('Sort');
    }

    public function validate()
    {
        $result = parent::validate();

        if (!$this->Title) {
            $result->addError(
                'Customer Account title is required.',
                ValidationResult::TYPE_ERROR
            );
        }

        return $result;
    }
}
