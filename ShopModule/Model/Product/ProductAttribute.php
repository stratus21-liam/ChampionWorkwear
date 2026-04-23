<?php

namespace ShopModule\Model;

use SilverStripe\ORM\DataObject;
use SilverStripe\ORM\ValidationResult;
use SilverStripe\Forms\CheckboxField;
use SilverStripe\Forms\DropdownField;
use SilverStripe\Forms\NumericField;
use SilverStripe\Forms\TextareaField;
use SilverStripe\Forms\TextField;
use SilverStripe\Forms\GridField\GridField;
use SilverStripe\Forms\GridField\GridFieldConfig_RecordEditor;
use Symbiote\GridFieldExtensions\GridFieldOrderableRows;

class ProductAttribute extends DataObject
{
    private static $table_name = 'ShopModule_ProductAttribute';

    private static $singular_name = 'Product Attribute';

    private static $plural_name = 'Product Attributes';

    private static $db = [
        'Title' => 'Varchar(255)',
        'Code' => 'Varchar(100)',
        'Type' => "Enum('colour_radio,square_radio,text_input','square_radio')",
        'Required' => 'Boolean',
        'Active' => 'Boolean',
        'Placeholder' => 'Varchar(255)',
        'MaxLength' => 'Int',
        'SortOrder' => 'Int',
    ];

    private static $has_many = [
        'Options' => ProductAttributeOption::class,
    ];
    
    private static $belongs_many_many = [
        'Products' => Product::class,
    ];

    private static $summary_fields = [
        'Title' => 'Title',
        'Code' => 'Code',
        'TypeNice' => 'Type',
        'Active.Nice' => 'Active',
    ];

    private static $default_sort = 'SortOrder ASC, Title ASC';

    public function getCMSFields()
    {
        $fields = parent::getCMSFields();

        $fields->removeByName([
            'Options',
            'Products'
        ]);

        // Remove the default SortOrder field as it will be handled by the GridField
        $fields->removeByName('SortOrder');

        $placeholder = TextField::create('Placeholder', 'Placeholder')
            ->setDescription('Only used for text input attributes.')
            ->displayIf('Type')->isEqualTo('text_input')->end();

        $maxLength = NumericField::create('MaxLength', 'Max Length')
            ->setDescription('Only used for text input attributes.')
            ->displayIf('Type')->isEqualTo('text_input')->end();

        $fields->addFieldsToTab('Root.Main', [
            TextField::create('Title', 'Title'),
            TextField::create('Code', 'Code')
                ->setDescription('Example: size, colour, name'),
            DropdownField::create('Type', 'Type', $this->getTypeOptions())
                ->setEmptyString('-- Select type --'),
            CheckboxField::create('Active', 'Active'),
            CheckboxField::create('Required', 'Required to select option before adding to cart?'),
            $placeholder,
            $maxLength,
        ]);

        if ($this->Type && $this->Type !== 'text_input') {
            // Add the GridField for sorting options
            $config = GridFieldConfig_RecordEditor::create();
            $config->addComponent(new GridFieldOrderableRows('SortOrder')); // Enable sorting
            $gridField = GridField::create(
                'Options',  // Field name
                'Options',  // Field title
                $this->Options(),  // Related records
                $config
            );

            // Add the GridField to the fields
            $fields->addFieldToTab('Root.Main', $gridField);            
        }



        return $fields;
    }

    public function getTypeOptions(): array
    {
        return [
            'colour_radio' => 'Colour Radio',
            'square_radio' => 'Square Radio',
            'text_input' => 'Text Input',
        ];
    }

    public function getTypeNice(): string
    {
        $types = $this->getTypeOptions();
        return $types[$this->Type] ?? $this->Type;
    }

    protected function onBeforeWrite()
    {
        parent::onBeforeWrite();

        // Auto-fill from Title if empty
        if (!$this->Code && $this->Title) {
            $this->Code = $this->Title;
        }

        if ($this->Code) {
            // lowercase
            $code = strtolower($this->Code);

            // replace spaces with underscores
            $code = preg_replace('/\s+/', '_', $code);

            // remove anything not a-z, 0-9 or underscore
            $code = preg_replace('/[^a-z0-9_]/', '', $code);

            // remove duplicate underscores
            $code = preg_replace('/_+/', '_', $code);

            // trim underscores from start/end
            $code = trim($code, '_');

            $this->Code = $code;
        }
    }

    public function validate()
    {
        $result = parent::validate();

        if (!$this->Title) {
            $result->addError('Title is required.', ValidationResult::TYPE_ERROR);
        }

        if (!$this->Code) {
            $result->addError('Code is required.', ValidationResult::TYPE_ERROR);
        }

        // Ensure code is unique
        if ($this->Code) {
            $existing = self::get()
                ->filter('Code', $this->Code)
                ->exclude('ID', $this->ID)
                ->first();

            if ($existing) {
                $result->addError(
                    sprintf('Code "%s" already exists.', $this->Code),
                    ValidationResult::TYPE_ERROR
                );
            }
        }

        if (!$this->Type) {
            $result->addError('Type is required.', ValidationResult::TYPE_ERROR);
        }

        if ($this->Type === 'text_input' && $this->MaxLength && $this->MaxLength < 1) {
            $result->addError('Max Length must be greater than 0.', ValidationResult::TYPE_ERROR);
        }

        return $result;
    }
}