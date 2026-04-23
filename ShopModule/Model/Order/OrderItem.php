<?php

namespace App\Model;

use ShopModule\Model\Product;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\HeaderField;
use SilverStripe\Forms\LiteralField;
use SilverStripe\Forms\ReadonlyField;
use SilverStripe\ORM\DataObject;
use SilverStripe\ORM\ValidationResult;
use SilverStripe\Security\Permission;

class OrderItem extends DataObject
{
    private static $table_name = 'ShopOrderItem';

    private static $db = [
        'ProductTitle' => 'Varchar(255)',
        'SKU' => 'Varchar(255)',
        'Quantity' => 'Int',
        'UnitPrice' => 'Currency',
        'LineTotal' => 'Currency',
        'OptionsJSON' => 'Text',
    ];

    private static $has_one = [
        'Order' => Order::class,
        'Product' => Product::class,
    ];

    private static $summary_fields = [
        'ProductTitle' => 'Product',
        'SKU' => 'SKU',
        'Quantity' => 'Qty',
        'UnitPrice.Nice' => 'Unit price',
        'LineTotal.Nice' => 'Line total',
    ];

    private static $searchable_fields = [
        'ProductTitle',
        'SKU',
        'Order.OrderNumber',
    ];

    public function onBeforeWrite()
    {
        parent::onBeforeWrite();

        $qty = max(1, (int) $this->Quantity);
        $price = max(0, (float) $this->UnitPrice);

        $this->Quantity = $qty;
        $this->UnitPrice = $price;
        $this->LineTotal = $qty * $price;
    }

    public function validate()
    {
        $result = parent::validate();

        if ((int) $this->Quantity < 1) {
            $result->addError('Quantity must be at least 1.');
        }

        if ((float) $this->UnitPrice < 0) {
            $result->addError('Unit price cannot be negative.');
        }

        return $result;
    }

    public function getCMSFields()
    {
        $fields = FieldList::create();

        $fields->push(HeaderField::create('ItemHeader', 'Order item'));
        $fields->push(ReadonlyField::create('OrderNumber', 'Order number', $this->Order()->exists() ? $this->Order()->OrderNumber : ''));
        $fields->push(ReadonlyField::create('ProductTitle', 'Product', $this->ProductTitle));
        $fields->push(ReadonlyField::create('SKU', 'SKU', $this->SKU));
        $fields->push(ReadonlyField::create('Quantity', 'Quantity', $this->Quantity));
        $fields->push(ReadonlyField::create('UnitPriceNice', 'Unit price', $this->dbObject('UnitPrice')->Nice()));
        $fields->push(ReadonlyField::create('LineTotalNice', 'Line total', $this->dbObject('LineTotal')->Nice()));

        $optionsHtml = '<p>No options selected.</p>';
        $options = $this->getOptionsList();

        if (!empty($options)) {
            $optionsHtml = '<ul>';
            foreach ($options as $option) {
                $optionsHtml .= '<li><strong>' . htmlspecialchars($option['Label'] ?? '') . ':</strong> ' . htmlspecialchars($option['Value'] ?? '') . '</li>';
            }
            $optionsHtml .= '</ul>';
        }

        $fields->push(HeaderField::create('OptionsHeader', 'Selected options'));
        $fields->push(LiteralField::create('OptionsList', $optionsHtml));

        return $fields;
    }

    public function getOptionsArray(): array
    {
        $json = trim((string) $this->OptionsJSON);

        if ($json === '') {
            return [];
        }

        $decoded = json_decode($json, true);

        return is_array($decoded) ? $decoded : [];
    }

    public function getOptionsList(): array
    {
        $options = [];
        $decoded = $this->getOptionsArray();

        foreach ($decoded as $key => $value) {
            if (is_array($value)) {
                $label = (string) ($value['label'] ?? $key);
                $optionValue = $value['value'] ?? '';

                if (is_array($optionValue)) {
                    $optionValue = implode(', ', $optionValue);
                }

                $options[] = [
                    'Key' => $key,
                    'Label' => $label,
                    'Value' => (string) $optionValue,
                ];
            } else {
                $options[] = [
                    'Key' => $key,
                    'Label' => (string) $key,
                    'Value' => (string) $value,
                ];
            }
        }

        return $options;
    }

    public function getOptionsNice(): string
    {
        $parts = [];

        foreach ($this->getOptionsList() as $option) {
            if (($option['Value'] ?? '') === '') {
                continue;
            }

            $parts[] = $option['Label'] . ': ' . $option['Value'];
        }

        return implode(' | ', $parts);
    }

    public function canView($member = null)
    {
        return Permission::check('CMS_ACCESS', 'any', $member);
    }

    public function canEdit($member = null)
    {
        return false;
    }

    public function canDelete($member = null)
    {
        return false;
    }

    public function canCreate($member = null, $context = [])
    {
        return false;
    }
}