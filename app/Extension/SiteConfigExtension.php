<?php

namespace App\Extension;

use App\Model\OrderNotificationRecipient;
use SilverStripe\AssetAdmin\Forms\UploadField;
use SilverStripe\Assets\Image;
use SilverStripe\Forms\GridField\GridField;
use SilverStripe\Forms\GridField\GridFieldConfig_RecordEditor;
use SilverStripe\ORM\DataExtension;

class SiteConfigExtension extends DataExtension
{
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
}