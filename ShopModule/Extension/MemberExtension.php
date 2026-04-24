<?php

namespace ShopModule\Extension;

use App\Model\Order;
use ShopModule\Model\CustomerAccount;
use ShopModule\Model\MemberAddress;
use ShopModule\Model\Role;
use SilverStripe\Control\Controller;
use SilverStripe\Core\Extension;
use SilverStripe\Forms\CheckboxField;
use SilverStripe\Forms\CurrencyField;
use SilverStripe\Forms\DropdownField;
use SilverStripe\Forms\FieldGroup;
use SilverStripe\Forms\ReadonlyField;
use SilverStripe\ORM\DataExtension;
use SilverStripe\ORM\ValidationResult;
use SilverStripe\Security\Permission;
use SilverStripe\View\Requirements;

class MemberExtension extends DataExtension
{
    private static $db = [
        'IsAdmin' => 'Boolean',
        'EnableSpendLimit' => 'Boolean',
        'SpendLimit' => 'Currency',
        'RequiresApproval' => 'Boolean',
        'Active' => 'Boolean'
    ];

    private static $has_one = [
        'CustomerAccount' => CustomerAccount::class,
        'Role' => Role::class
    ];

    private static $has_many = [
        'Orders' => Order::class . '.Customer',
        // Pre Saved Addresses Development: allow each customer/member to manage multiple reusable checkout addresses.
        'SavedAddresses' => MemberAddress::class . '.Member',
    ];

    private static $summary_fields = [
        'Name' => 'Name',
        'Email' => 'Email',
        'CustomerAccount.Title' => 'Customer Account',
        'Role.Title' => 'Role',
        'IsAdmin.Nice' => 'Admin',
        'RequiresApproval.Nice' => 'Requires Approval',
        'EnableSpendLimit.Nice' => 'Spend Limit Enabled',
        'SpendLimit.Nice' => 'Spend Limit',
        'Active.Nice' => 'Active'
    ];

    public function updateCMSFields($fields)
    {
        $fields->removeByName([
            'CustomerAccountID',
            'RoleID',
            'IsAdmin',
            'EnableSpendLimit',
            'SpendLimit',
            'RequiresApproval',
            'Active',
            'Locale',
            'FailedLoginCount',
            // 'DirectGroups',
        ]);

        $owner = $this->owner;
        $customerAccountId = (int) $owner->CustomerAccountID;

        if ($customerAccountId && $owner->CustomerAccount()->exists()) {
            $fields->addFieldToTab(
                'Root.Main',
                ReadonlyField::create(
                    'CustomerAccountName',
                    'Customer Account',
                    $owner->CustomerAccount()->Title
                )
            );
        }

        $roleSource = [];
        if ($customerAccountId) {
            $roleSource = Role::get()
                ->filter('CustomerAccountID', $customerAccountId)
                ->sort('Title')
                ->map('ID', 'Title')
                ->toArray();
        }

        $enableSpendLimitField = CheckboxField::create(
            'EnableSpendLimit',
            'Enable spend limit'
        );

        $spendLimitField = CurrencyField::create('SpendLimit', 'Spend Limit');

        $spendLimitGroup = FieldGroup::create(
            'Spend limit',
            $enableSpendLimitField,
            $spendLimitField
        );

        $fields->addFieldsToTab('Root.Main', [
            CheckboxField::create('IsAdmin', 'Is Customer Account Admin'),
            DropdownField::create('RoleID', 'Role', $roleSource)
                ->setEmptyString('-- Select role --'),
            $spendLimitGroup,
            CheckboxField::create('RequiresApproval', 'Requires Approval On Orders'),
            CheckboxField::create('Active', 'Active')
        ]);
       
    }

    public function SavedAddresses()
    {
        // Pre Saved Addresses Development: expose saved addresses even when extension has_many methods are not available from Member->__call().
        if (!$this->owner->ID) {
            return MemberAddress::get()->filter('ID', 0);
        }

        return MemberAddress::get()
            ->filter('MemberID', $this->owner->ID)
            ->sort('Title', 'ASC');
    }

    /**
     * Just checks if member has an account
     */
    public function HasCustomerAccount(): bool
    {
        if ($this->owner->inGroup('administrators')) {
            return true;
        }

        $account = $this->owner->CustomerAccount();

        return $account && $account->exists();
    }

    /**
     * Check if linked account is active (admins bypass)
     */
    public function HasActiveCustomerAccount(): bool
    {
        if ($this->owner->inGroup('administrators')) {
            return true;
        }

        $account = $this->owner->CustomerAccount();

        return $account && $account->exists() && (bool) $account->Active;
    }

    /**
     * Check if account is active (admins bypass)
     */
    public function MemberAccountActive(): bool
    {
        if ($this->owner->inGroup('administrators')) {
            return true;
        }

        return (bool) $this->owner->Active;
    }

    /**
     * Get account safely
     */
    public function getCustomerAccount()
    {
        if ($this->owner->HasActiveCustomerAccount()) {
            return $this->owner->CustomerAccount();
        }

        return null;
    }

    public function onBeforeWrite()
    {
        parent::onBeforeWrite();

        if (!$this->owner->exists() && $this->owner->Active === null) {
            $this->owner->Active = true;
        }

        if (!$this->owner->EnableSpendLimit) {
            $this->owner->SpendLimit = 0;
        }
    }

    public function validate(ValidationResult $result)
    {
        $owner = $this->owner;

        // if (!$owner->CustomerAccountID) {
        //     $result->addError('Customer Account is required.');
        // }

        // if (!$owner->Email) {
        //     $result->addError('Email is required.');
        // }

        // if (!$owner->FirstName) {
        //     $result->addError('First name is required.');
        // }

        // if (!$owner->Surname) {
        //     $result->addError('Surname is required.');
        // }

        // if (!$owner->IsAdmin && !$owner->RoleID) {
        //     $result->addError('Role is required for sub users.');
        // }

        // if ($owner->RoleID) {
        //     $role = Role::get()->byID($owner->RoleID);

        //     if ($role && (int) $role->CustomerAccountID !== (int) $owner->CustomerAccountID) {
        //         $result->addError('Selected role does not belong to this customer account.');
        //     }
        // }

        // if ($owner->SpendLimit !== null && $owner->SpendLimit !== '' && $owner->SpendLimit < 0) {
        //     $result->addError('Spend limit cannot be less than 0.');
        // }

        // if ($owner->EnableSpendLimit) {
        //     if ($owner->SpendLimit === null || $owner->SpendLimit === '' || (float) $owner->SpendLimit <= 0) {
        //         $result->addError('Spend limit must be greater than 0 when spend limit is enabled.');
        //     }
        // }

        return $result;
    }
}
