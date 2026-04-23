<?php

namespace ShopModule\Model;

use SilverStripe\ORM\DataObject;
use SilverStripe\ORM\ValidationResult;

class Role extends DataObject
{
    private static $singular_name = 'Role';

    private static $plural_name = 'Roles';

    private static $table_name = 'ShopModule_Role';

    private static $db = [
        'Title' => 'Varchar(255)',
        'Description' => 'Text',
        'SortOrder' => 'Int',
        // 'Active' => 'Boolean'
    ];

    private static $summary_fields = [
        'Title' => 'Title',
        'CustomerAccount.Title' => 'Customer Account',
        // 'Active.Nice' => 'Active'
    ];

    private static $searchable_fields = [
        'Title'
    ];

    private static $has_one = [
        'CustomerAccount' => CustomerAccount::class
    ];

    private static $belongs_many_many = [
        'Products' => Product::class
    ];

    private static $default_sort = 'SortOrder ASC, Title ASC';

    public function getCMSFields()
    {
        $fields = parent::getCMSFields();

        $fields->removeByName([
            'CustomerAccountID',
            'Products',
            'SortOrder',
        ]);

        if ($this->CustomerAccountID && $this->CustomerAccount()->exists()) {
            $fields->addFieldToTab(
                'Root.Main',
                \SilverStripe\Forms\ReadonlyField::create(
                    'CustomerAccountName',
                    'Customer Account',
                    $this->CustomerAccount()->Title
                ),
                'Title'
            );
        }

        return $fields;
    }

    public function validate()
    {
        $result = parent::validate();

        if (!$this->Title) {
            $result->addError(
                'Role title is required.',
                ValidationResult::TYPE_ERROR
            );
        }

        if (!$this->CustomerAccountID) {
            $result->addError(
                'Customer Account is required.',
                ValidationResult::TYPE_ERROR
            );
        }

        return $result;
    }
}