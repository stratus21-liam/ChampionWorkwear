<?php

namespace App\Model;

use SilverStripe\Forms\ReadonlyField;
use SilverStripe\ORM\DataObject;
use SilverStripe\ORM\ValidationResult;
use SilverStripe\SiteConfig\SiteConfig;

class OrderNotificationRecipient extends DataObject
{
    private static $table_name = 'OrderNotificationRecipient';

    private static $db = [
        'Title' => 'Varchar(255)',
        'Email' => 'Varchar(255)',
        'Active' => 'Boolean',
    ];

    private static $has_one = [
        'SiteConfig' => SiteConfig::class,
    ];

    private static $summary_fields = [
        'Title' => 'Name',
        'Email' => 'Email',
        'Active.Nice' => 'Active',
    ];

    private static $default_sort = 'Title ASC';

    public function populateDefaults()
    {
        parent::populateDefaults();
        $this->Active = true;
    }

    public function validate()
    {
        $result = parent::validate();

        if (trim((string) $this->Title) === '') {
            $result->addError('Name is required.');
        }

        if (trim((string) $this->Email) === '') {
            $result->addError('Email is required.');
        } elseif (!filter_var($this->Email, FILTER_VALIDATE_EMAIL)) {
            $result->addError('Please enter a valid email address.');
        }

        return $result;
    }
}