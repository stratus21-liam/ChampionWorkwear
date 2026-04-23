<?php

namespace ShopModule\Model;

use SilverStripe\ORM\DataObject;
use SilverStripe\ORM\ValidationResult;
use SilverStripe\Security\Member;

class MemberAddress extends DataObject
{
    private static $singular_name = 'Member Address';

    private static $plural_name = 'Member Addresses';

    private static $table_name = 'ShopModule_MemberAddress';

    // Pre Saved Addresses Development: store reusable delivery addresses against the logged-in customer.
    private static $db = [
        'Title' => 'Varchar(255)',
        'DeliveryCompany' => 'Varchar(255)',
        'DeliveryContactName' => 'Varchar(255)',
        'DeliveryPhone' => 'Varchar(50)',
        'DeliveryEmail' => 'Varchar(255)',
        'DeliveryAddressLine1' => 'Varchar(255)',
        'DeliveryAddressLine2' => 'Varchar(255)',
        'DeliveryCity' => 'Varchar(255)',
        'DeliveryCounty' => 'Varchar(255)',
        'DeliveryPostcode' => 'Varchar(30)',
    ];

    private static $has_one = [
        'Member' => Member::class,
    ];

    private static $summary_fields = [
        'Title' => 'Title',
        'DeliveryCompany' => 'Company',
        'DeliveryContactName' => 'Contact',
        'DeliveryCity' => 'Town / City',
        'DeliveryPostcode' => 'Postcode',
    ];

    private static $default_sort = 'Title ASC';

    public static function getDeliveryFieldNames(): array
    {
        // Pre Saved Addresses Development: keep account and checkout saved-address handling on one field list.
        return [
            'Title',
            'DeliveryCompany',
            'DeliveryContactName',
            'DeliveryPhone',
            'DeliveryEmail',
            'DeliveryAddressLine1',
            'DeliveryAddressLine2',
            'DeliveryCity',
            'DeliveryCounty',
            'DeliveryPostcode',
        ];
    }

    public static function normaliseAddressData(array $data): array
    {
        // Pre Saved Addresses Development: trim saved-address input in one place for account and checkout.
        $normalised = [];

        foreach (self::getDeliveryFieldNames() as $field) {
            $normalised[$field] = trim((string) ($data[$field] ?? ''));
        }

        return $normalised;
    }

    public static function validateAddressData(array $data): ?string
    {
        // Pre Saved Addresses Development: mirror the Order delivery requirements so saved addresses can be created consistently.
        $data = self::normaliseAddressData($data);
        $required = [
            'Title' => 'Address name',
            'DeliveryCompany' => 'Company name',
            'DeliveryContactName' => 'Contact name',
            'DeliveryPhone' => 'Phone',
            'DeliveryEmail' => 'Email',
            'DeliveryAddressLine1' => 'Address line 1',
            'DeliveryCity' => 'Town / City',
            'DeliveryCounty' => 'County',
            'DeliveryPostcode' => 'Postcode',
        ];

        foreach ($required as $field => $label) {
            if ($data[$field] === '') {
                return $label . ' is required.';
            }
        }

        if (!filter_var($data['DeliveryEmail'], FILTER_VALIDATE_EMAIL)) {
            return 'Please enter a valid email address.';
        }

        return null;
    }

    public static function writeAddressFromData(MemberAddress $address, array $data): ?string
    {
        // Pre Saved Addresses Development: one write helper avoids separate account and checkout save logic.
        $data = self::normaliseAddressData($data);
        $message = self::validateAddressData($data);

        if ($message !== null) {
            return $message;
        }

        foreach ($data as $field => $value) {
            $address->$field = $value;
        }

        $address->write();

        return null;
    }

    public static function createForMemberFromData(Member $member, array $data): array
    {
        // Pre Saved Addresses Development: shared create helper used by My Account and checkout.
        $address = MemberAddress::create();
        $address->MemberID = (int) $member->ID;

        $message = self::writeAddressFromData($address, $data);

        return [$address, $message];
    }

    public function validate()
    {
        $result = parent::validate();

        // Pre Saved Addresses Development: DataObject validation uses the same shared saved-address validator.
        $message = self::validateAddressData($this->toMap());

        if ($message !== null) {
            $result->addError($message);
        }

        return $result;
    }

    public function getDropdownTitle(): string
    {
        // Pre Saved Addresses Development: make checkout dropdown labels useful without needing template string logic.
        $parts = array_filter([
            $this->Title,
            $this->DeliveryAddressLine1,
            $this->DeliveryCity,
            $this->DeliveryPostcode,
        ]);

        return implode(' - ', $parts);
    }
}
