<?php

namespace App\Extension;

use App\Model\OrderNotificationRecipient;
use SilverStripe\AssetAdmin\Forms\UploadField;
use SilverStripe\Assets\Image;
use SilverStripe\Forms\GridField\GridField;
use SilverStripe\Forms\GridField\GridFieldConfig_RecordEditor;
use SilverStripe\Forms\NumericField;
use SilverStripe\ORM\DataExtension;
use SilverStripe\ORM\ValidationResult;

class SiteConfigExtension extends DataExtension
{
    private static $db = [
        'ProductCMSPagination' => 'Int',
    ];

    private static $defaults = [
        'ProductCMSPagination' => 12,
    ];

    private static $has_one = [
        'BannerImage' => Image::class,
    ];

    private static $has_many = [
        'OrderNotificationRecipients' => OrderNotificationRecipient::class,
    ];

    private static $owns = [
        'OrderNotificationRecipients',
        'BannerImage'
    ];

    public function updateCMSFields($fields)
    {
        $productCMSPagination = NumericField::create("ProductCMSPagination", "ProductCMSPagination")
            ->setAttribute('required', true);

        $fields->addFieldToTab("Root.ProductPaginate", $productCMSPagination);

        $fields->addFieldToTab("Root.BannerImage", new UploadField("BannerImage", "Banner Image"));

        $fields->addFieldToTab(
            'Root.OrderNotifications',
            GridField::create(
                'OrderNotificationRecipients',
                'Order notification recipients',
                $this->owner->OrderNotificationRecipients(),
                GridFieldConfig_RecordEditor::create()
            )
        );
    }

    public function validate(ValidationResult $result)
    {
        if ($this->owner->ProductCMSPagination === null || $this->owner->ProductCMSPagination === '') {
            $result->addError('Product CMS Pagination is required.', ValidationResult::TYPE_ERROR);
        }

        return $result;
    }
}
