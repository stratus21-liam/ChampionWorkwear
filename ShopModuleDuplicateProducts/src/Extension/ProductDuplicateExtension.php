<?php

namespace ShopModuleDuplicateProducts\Extension;

use ShopModule\Model\CustomerAccount;
use SilverStripe\Control\Controller;
use SilverStripe\Control\Director;
use SilverStripe\Forms\DropdownField;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\LiteralField;
use SilverStripe\ORM\DataExtension;
use SilverStripe\ORM\ValidationResult;
use SilverStripe\Core\Convert;

class ProductDuplicateExtension extends DataExtension
{
    private const SESSION_MESSAGE_KEY = 'ShopModuleDuplicateProducts.DuplicateMessage';
    private const SESSION_MESSAGE_TYPE_KEY = 'ShopModuleDuplicateProducts.DuplicateMessageType';

    public function updateCMSFields(FieldList $fields): void
    {
        if (!$this->owner->ID) {
            return;
        }

        $duplicateFields = [
            LiteralField::create('ProductDuplicateMessage', $this->getMessageHTML()),
        ];

        $accountSource = CustomerAccount::get()
            ->exclude('ID', (int) $this->owner->CustomerAccountID)
            ->sort('Title')
            ->map('ID', 'Title')
            ->toArray();

        if ($accountSource) {
            $duplicateFields[] = DropdownField::create(
                'DuplicateTargetCustomerAccountID',
                'Target customer account',
                $accountSource
            )->setEmptyString('Select an account');

            $duplicateFields[] = LiteralField::create(
                'ProductDuplicateBackURL',
                sprintf(
                    '<input type="hidden" name="BackURL" value="%s" />',
                    $this->escapeAttribute($this->getBackURL())
                )
            );

            $duplicateFields[] = LiteralField::create(
                'ProductDuplicateAction',
                $this->getDuplicateActionHTML()
            );
        } else {
            $duplicateFields[] = LiteralField::create(
                'ProductDuplicateNoAccounts',
                '<p>No other customer accounts are available.</p>'
            );
        }

        $fields->addFieldsToTab('Root.Duplicate', $duplicateFields);
    }

    public static function setDuplicateMessage(string $message, string $type = ValidationResult::TYPE_GOOD): void
    {
        $request = Controller::curr() ? Controller::curr()->getRequest() : null;

        if (!$request || !$request->getSession()) {
            return;
        }

        $request->getSession()->set(self::SESSION_MESSAGE_KEY, $message);
        $request->getSession()->set(self::SESSION_MESSAGE_TYPE_KEY, $type);
    }

    private function getMessageHTML(): string
    {
        $request = Controller::curr() ? Controller::curr()->getRequest() : null;

        if (!$request || !$request->getSession()) {
            return '';
        }

        $session = $request->getSession();
        $message = $session->get(self::SESSION_MESSAGE_KEY);
        $type = $session->get(self::SESSION_MESSAGE_TYPE_KEY) ?: ValidationResult::TYPE_GOOD;

        $session->clear(self::SESSION_MESSAGE_KEY);
        $session->clear(self::SESSION_MESSAGE_TYPE_KEY);

        if (!$message) {
            return '';
        }

        $class = $type === ValidationResult::TYPE_GOOD ? 'good' : 'bad';

        return sprintf(
            '<p class="message %s">%s</p>',
            $this->escapeAttribute($class),
            $this->escapeText($message)
        );
    }

    private function getDuplicateActionHTML(): string
    {
        $action = Controller::join_links(
            Director::baseURL(),
            'admin/product-duplicate/duplicate',
            (int) $this->owner->ID
        );

        return sprintf(
            '<button type="button" class="btn btn-primary font-icon-page-multiple" data-duplicate-action="%s" onclick="return (function(button) {
                var root = button.closest(\'#Root_Duplicate\') || document;
                var target = root.querySelector(\'[name=&quot;DuplicateTargetCustomerAccountID&quot;]\');
                var security = document.querySelector(\'input[name=&quot;SecurityID&quot;]\');
                var backURL = root.querySelector(\'input[name=&quot;BackURL&quot;]\');

                if (!target || !target.value) {
                    alert(\'Please choose a target customer account.\');
                    return false;
                }

                var form = document.createElement(\'form\');
                form.method = \'post\';
                form.action = button.getAttribute(\'data-duplicate-action\');

                function addInput(name, value) {
                    var input = document.createElement(\'input\');
                    input.type = \'hidden\';
                    input.name = name;
                    input.value = value || \'\';
                    form.appendChild(input);
                }

                addInput(\'DuplicateTargetCustomerAccountID\', target.value);

                if (security) {
                    addInput(\'SecurityID\', security.value);
                }

                if (backURL) {
                    addInput(\'BackURL\', backURL.value);
                }

                document.body.appendChild(form);
                form.submit();
                return false;
            })(this);">Duplicate product</button>',
            $this->escapeAttribute($action)
        );
    }

    private function getBackURL(): string
    {
        $request = Controller::curr() ? Controller::curr()->getRequest() : null;

        return $request ? $request->getURL(true) . '#Root_Duplicate' : '';
    }

    private function escapeText(string $value): string
    {
        return Convert::raw2xml($value);
    }

    private function escapeAttribute(string $value): string
    {
        return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
    }
}
