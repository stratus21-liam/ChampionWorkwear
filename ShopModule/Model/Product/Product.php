<?php

namespace ShopModule\Model;

use App\Extension\Sluggable;
use App\Pages\ProductPage;
use SilverStripe\ORM\DataObject;
use SilverStripe\ORM\ValidationResult;
use SilverStripe\Assets\Image;
use SilverStripe\Forms\CheckboxField;
use SilverStripe\Forms\TextField;
use SilverStripe\Forms\CurrencyField;
use SilverStripe\TagField\TagField;
use Bummzack\SortableFile\Forms\SortableUploadField;
use SilverStripe\Control\Controller;
use SilverStripe\Forms\HTMLEditor\HTMLEditorField;
use SilverStripe\Forms\ReadonlyField;
use SilverStripe\ORM\ArrayList;
use SilverStripe\View\ArrayData;

class Product extends DataObject
{
    private static $singular_name = 'Product';

    private static $plural_name = 'Products';

    private static $table_name = 'ShopModule_Product';

    private static $page_class = ProductPage::class;

    private static $db = [
        'Title' => 'Varchar(255)',
        'Description' => 'HTMLText',
        'SKU' => 'Varchar(100)',
        'Price' => 'Currency',
        'Active' => 'Boolean'
    ];

    private static $summary_fields = [
        'Title' => 'Title',
        'SKU' => 'SKU',
        'Price.Nice' => 'Price',
        'RolesList' => 'Roles',
        'AttributesList' => 'Attributes',
        'Active.Nice' => 'Active'
    ];

    private static $searchable_fields = [
        'Title',
        'SKU'
    ];

    private static $extensions = [
        Sluggable::class,
    ];

    private static $has_one = [
        'CustomerAccount' => CustomerAccount::class,
        'FeaturedImage'   => Image::class,
    ];

    private static $many_many = [
        'Roles' => Role::class,
        'Images' => Image::class,
        'Attributes' => ProductAttribute::class,
        'AttributeOptions' => ProductAttributeOption::class,
    ];

    private static $many_many_extraFields = [
        'Images' => [
            'SortOrder' => 'Int'
        ]
    ];

    private static $owns = [
        'Images',
        'FeaturedImage'
    ];

    private static $default_sort = 'Title ASC';

    public function getCMSFields()
    {
        $fields = parent::getCMSFields();

        $fields->removeByName([
            'Roles',
            'Images',
            'Attributes',
            'AttributeOptions'
        ]);

        $fields->addFieldsToTab('Root.Main', [
            TextField::create('Title', 'Title'),
            TextField::create('SKU', 'SKU'),
            CurrencyField::create('Price', 'Price'),
            HTMLEditorField::create('Description', 'Description'),
            CheckboxField::create('Active', 'Active')
        ]);

        $fields->addFieldToTab(
            'Root.Main',
            SortableUploadField::create('Images', 'Images')
        );

        $roleSource = Role::get()->filter('ID', 0);

        if ($this->CustomerAccountID) {
            $roleSource = Role::get()->filter('CustomerAccountID', $this->CustomerAccountID);
        }

        $fields->addFieldToTab(
            'Root.Main',
            TagField::create(
                'Roles',
                'Roles',
                $roleSource,
                $this->Roles()
            )
                ->setTitleField('Title')
                ->setShouldLazyLoad(false)
                ->setCanCreate(false)
                ->setDescription(
                    $this->CustomerAccountID
                        ? 'Only roles for this customer account are shown.'
                        : 'Save the product with a customer account first before assigning roles.'
                )
        );

        $fields->addFieldToTab(
            'Root.Attributes',
            TagField::create(
                'Attributes',
                'Attributes',
                ProductAttribute::get()->filter('Active', 1),
                $this->Attributes()
            )
                ->setTitleField('Title')
                ->setShouldLazyLoad(false)
                ->setCanCreate(false)
                ->setDescription('Select which attributes this product should use, e.g. Size, Colour, Name. Save first, then choose allowed values below.')
        );

        $optionSource = ProductAttributeOption::get()->filter('Active', 1);

        // Only show options for selected attributes
        if ($this->Attributes()->exists()) {
            $optionSource = $optionSource->filter([
                'AttributeID' => $this->Attributes()->column('ID')
            ]);
        } else {
            // No attributes selected yet = no options available
            $optionSource = $optionSource->filter('ID', 0);
        }

        $fields->addFieldToTab(
            'Root.Attributes',
            TagField::create(
                'AttributeOptions',
                'Allowed Attribute Values',
                $optionSource,
                $this->AttributeOptions()
            )
                ->setTitleField('Title')
                ->setShouldLazyLoad(false)
                ->setCanCreate(false)
                ->setDescription('Only values from the selected attributes are shown. Text input attributes do not use values.')
        );

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

    public function Images()
    {
        return $this->getManyManyComponents('Images')->sort('SortOrder');
    }

    public function SortedImages()
    {
        return $this->Images()->sort('SortOrder');
    }

    public function getRolesList()
    {
        $titles = $this->Roles()->column('Title');

        return $titles ? implode(', ', $titles) : '';
    }

    public function getAttributesList()
    {
        $titles = $this->Attributes()->column('Title');

        return $titles ? implode(', ', $titles) : '';
    }

    /**
     * @param [type] $action
     *
     * @return void
     */
    public function Link($action = null)
    {
        if ($page = ProductPage::get()->first()) {
            return Controller::join_links(
                $page->Link(),
                $this->URLSegment,
                $action
            );
        }
    }

    /**
     * Return selected options grouped by attribute
     */
    public function getGroupedAttributeOptions()
    {
        $grouped = [];

        foreach ($this->AttributeOptions()->sort('Attribute.Title, SortOrder, Title') as $option) {
            if (!$option->AttributeID || !$option->Attribute()->exists()) {
                continue;
            }

            $attributeId = $option->AttributeID;

            if (!isset($grouped[$attributeId])) {
                $grouped[$attributeId] = [
                    'Attribute' => $option->Attribute(),
                    'Options' => ArrayList::create(),
                ];
            }

            $grouped[$attributeId]['Options']->push($option);
        }

        $list = ArrayList::create();

        foreach ($grouped as $group) {
            $list->push(ArrayData::create([
                'Attribute' => $group['Attribute'],
                'Options' => $group['Options'],
            ]));
        }

        return $list;
    }

    /**
     * Hard-enforce valid option assignments after save.
     * This cleans up invalid TagField selections that may still get posted.
     */
    protected function onAfterWrite()
    {
        parent::onAfterWrite();

        if (!$this->ID) {
            return;
        }

        $selectedAttributeIds = $this->Attributes()->column('ID');

        foreach ($this->AttributeOptions() as $option) {
            $remove = false;

            if (!$option->AttributeID) {
                $remove = true;
            }

            if (!$remove && !in_array($option->AttributeID, $selectedAttributeIds)) {
                $remove = true;
            }

            if (
                !$remove &&
                $option->Attribute()->exists() &&
                $option->Attribute()->Type === 'text_input'
            ) {
                $remove = true;
            }

            if ($remove) {
                $this->AttributeOptions()->remove($option);
            }
        }
    }

    public function validate()
    {
        $result = parent::validate();

        if (!$this->Title) {
            $result->addError(
                'Product title is required.',
                ValidationResult::TYPE_ERROR
            );
        }

        if (!$this->SKU) {
            $result->addError(
                'SKU is required.',
                ValidationResult::TYPE_ERROR
            );
        }

        if ($this->Price !== null && $this->Price !== '' && $this->Price < 0) {
            $result->addError(
                'Price cannot be less than 0.',
                ValidationResult::TYPE_ERROR
            );
        }

        // SKU must be unique per customer account
        if ($this->SKU && $this->CustomerAccountID) {
            $existingProduct = self::get()
                ->filter([
                    'SKU' => $this->SKU,
                    'CustomerAccountID' => $this->CustomerAccountID,
                ])
                ->exclude('ID', $this->ID)
                ->first();

            if ($existingProduct) {
                $accountTitle = $this->CustomerAccount()->exists()
                    ? $this->CustomerAccount()->Title
                    : 'this customer account';

                $result->addError(
                    sprintf(
                        'SKU "%s" already exists for customer account "%s".',
                        $this->SKU,
                        $accountTitle
                    ),
                    ValidationResult::TYPE_ERROR
                );
            }
        }

        // Informational validation only - hard cleanup is done in onAfterWrite()
        if ($this->AttributeOptions()->exists()) {
            $selectedAttributeIds = $this->Attributes()->column('ID');

            foreach ($this->AttributeOptions() as $option) {
                if (!in_array($option->AttributeID, $selectedAttributeIds)) {
                    $result->addError(
                        sprintf(
                            'Option "%s" belongs to attribute "%s", but that attribute is not assigned to this product.',
                            $option->Title,
                            $option->Attribute()->Title
                        ),
                        ValidationResult::TYPE_ERROR
                    );
                }
            }
        }

        foreach ($this->Attributes() as $attribute) {
            if ($attribute->Type === 'text_input') {
                $hasOptionsForTextAttribute = $this->AttributeOptions()
                    ->filter('AttributeID', $attribute->ID)
                    ->exists();

                if ($hasOptionsForTextAttribute) {
                    $result->addError(
                        sprintf(
                            'Attribute "%s" is a text input and should not have selectable options assigned.',
                            $attribute->Title
                        ),
                        ValidationResult::TYPE_ERROR
                    );
                }
            }
        }

        return $result;
    }
}