<?php

namespace ShopModule\Model;

use SilverStripe\Colorpicker\Forms\ColorPickerField;
use SilverStripe\ORM\DataObject;
use SilverStripe\ORM\ValidationResult;
use SilverStripe\Forms\CheckboxField;
use SilverStripe\Forms\ReadonlyField;
use SilverStripe\Forms\TextField;
use SilverStripe\Forms\ColorField;
use TractorCow\Colorpicker\Forms\ColorField as FormsColorField;

class ProductAttributeOption extends DataObject
{
    private static $table_name = 'ShopModule_ProductAttributeOption';

    private static $singular_name = 'Product Attribute Option';

    private static $plural_name = 'Product Attribute Options';

    private static $db = [
        'Title' => 'Varchar(255)',
        'Value' => 'Varchar(255)',
        'HexColour' => 'Varchar(20)',
        'SquareLabel' => 'Varchar(50)',
        'Active' => 'Boolean',
        'SortOrder' => 'Int',
    ];

    private static $has_one = [
        'Attribute' => ProductAttribute::class,
    ];
    
    private static $belongs_many_many = [
        'Products' => Product::class,
    ];

    private static $summary_fields = [
        'Title' => 'Title',
        'Value' => 'Value',
        'Attribute.Title' => 'Attribute',
        'HexColour' => 'Hex Colour',
        'SquareLabel' => 'Square Label',
        'Active.Nice' => 'Active',
    ];

    private static $default_sort = 'SortOrder ASC, Title ASC';

    public function getCMSFields()
    {
        $fields = parent::getCMSFields();

        $fields->removeByName([
            'SortOrder',
            'HexColour',
            'SquareLabel',
            'AttributeID',
        ]);

        $attributeType = null;

        if ($this->AttributeID && $this->Attribute()->exists()) {
            $attributeType = $this->Attribute()->Type;

            $fields->addFieldToTab(
                'Root.Main',
                ReadonlyField::create(
                    'AttributeName',
                    'Attribute',
                    $this->Attribute()->Title . ' (' . $this->Attribute()->getTypeNice() . ')'
                ),
                'Title'
            );
        }

        $mainFields = [
            TextField::create('Title', 'Title'),
            TextField::create('Value', 'Value')
                ->setDescription('Stored value, e.g. red, blue, s, m, l'),
        ];

        if ($attributeType === 'colour_radio') {
            $mainFields[] = FormsColorField::create('HexColour', 'Hex Colour')
                ->setDescription('Choose the colour for this option.');
        }

        if ($attributeType === 'square_radio') {
            $mainFields[] = TextField::create('SquareLabel', 'Square Label')
                ->setDescription('Example: S, M, L');
        }

        $mainFields[] = CheckboxField::create('Active', 'Active');

        $fields->addFieldsToTab('Root.Main', $mainFields);

        return $fields;
    }

    public function getOptionLabel()
    {
        if ($this->AttributeID && $this->Attribute()->exists()) {
            return $this->Attribute()->Title . ' - ' . $this->Title;
        }

        return $this->Title;
    }

    protected function onBeforeWrite()
    {
        parent::onBeforeWrite();

        // Auto-fill from Title if empty
        if (!$this->Value && $this->Title) {
            $this->Value = $this->Title;
        }

        if ($this->Value) {
            // lowercase
            $value = strtolower($this->Value);

            // replace spaces with underscores
            $value = preg_replace('/\s+/', '_', $value);

            // remove anything not a-z, 0-9 or underscore
            $value = preg_replace('/[^a-z0-9_]/', '', $value);

            // remove duplicate underscores
            $value = preg_replace('/_+/', '_', $value);

            // trim underscores from start/end
            $value = trim($value, '_');

            $this->Value = $value;
        }
    }

    public function validate()
    {
        $result = parent::validate();

        if (!$this->Title) {
            $result->addError('Title is required.', ValidationResult::TYPE_ERROR);
        }

        if (!$this->Value) {
            $result->addError('Value is required.', ValidationResult::TYPE_ERROR);
        }

        if (!$this->AttributeID) {
            $result->addError('Attribute is required.', ValidationResult::TYPE_ERROR);
        }

        if ($this->AttributeID && $this->Attribute()->exists()) {
            $type = $this->Attribute()->Type;

            if ($type === 'colour_radio' && !$this->HexColour) {
                $result->addError('Hex Colour is required for colour radio options.', ValidationResult::TYPE_ERROR);
            }

            if ($type === 'square_radio' && !$this->SquareLabel) {
                $result->addError('Square Label is required for square radio options.', ValidationResult::TYPE_ERROR);
            }
        }

        return $result;
    }
}