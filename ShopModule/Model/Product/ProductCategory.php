<?php

namespace ShopModule\Model;

use SilverStripe\Forms\ReadonlyField;
use SilverStripe\ORM\DataObject;
use SilverStripe\ORM\ValidationResult;

class ProductCategory extends DataObject
{
    private static $singular_name = 'Product Category';

    private static $plural_name = 'Product Categories';

    private static $table_name = 'ShopModule_ProductCategory';

    private static $db = [
        'Title' => 'Varchar(255)',
        'SortOrder' => 'Int',
    ];

    private static $has_one = [
        'CustomerAccount' => CustomerAccount::class,
    ];

    private static $belongs_many_many = [
        'Products' => Product::class,
    ];

    private static $summary_fields = [
        'Title' => 'Title',
        'SortOrder' => 'Sort Order',
    ];

    private static $searchable_fields = [
        'Title',
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
                ReadonlyField::create(
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
                'Category title is required.',
                ValidationResult::TYPE_ERROR
            );
        }

        // if (!$this->CustomerAccountID) {
        //     $result->addError(
        //         'Customer Account is required.',
        //         ValidationResult::TYPE_ERROR
        //     );
        // }

        return $result;
    }
}
