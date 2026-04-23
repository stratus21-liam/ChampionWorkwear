<?php
namespace App\Pages;

use PageController;
use Page;
use App\Extension\SinglePageInstance;
use ShopModule\Model\MemberAddress;
use SilverStripe\ORM\ArrayList;
use SilverStripe\Security\Security;
use SilverStripe\Security\Member;
use SilverStripe\Control\HTTPRequest;
use SilverStripe\Control\HTTPResponse;

class AccountPage extends Page
{
    private static $singular_name = "Account Page";

    private static $icon_class = 'font-icon-torso';

    private static $db = [];

    private static $has_one = [];

    private static $extensions = [
        SinglePageInstance::class
    ];

    public function getCMSFields()
    {
        $fields = parent::getCMSFields();
        return $fields;
    }

    public function CurrentMember()
    {
        return Security::getCurrentUser();
    }
}

class AccountPageController extends PageController
{
    private const ACCOUNT_MESSAGE_SESSION_KEY = 'Shop.AccountMessage';

    private static $allowed_actions = [
        'saveAccount',
        'createAddress',
        'updateAddress',
        'deleteAddress',
    ];

    public function init()
    {
        parent::init();
    }

    protected function getAccountMessageSessionKey(): string
    {
        $member = Security::getCurrentUser();

        if ($member && $member->ID) {
            return self::ACCOUNT_MESSAGE_SESSION_KEY . '.Member.' . $member->ID;
        }

        return self::ACCOUNT_MESSAGE_SESSION_KEY . '.Guest';
    }

    protected function setAccountMessage(string $message): void
    {
        $this->getRequest()
            ->getSession()
            ->set($this->getAccountMessageSessionKey(), $message);
    }

    public function AccountMessage(): ?string
    {
        $session = $this->getRequest()->getSession();
        $messageKey = $this->getAccountMessageSessionKey();
        $message = $session->get($messageKey);

        if ($message) {
            $session->clear($messageKey);
        }

        return $message;
    }

    public function SavedAddresses()
    {
        // Pre Saved Addresses Development: expose the logged-in customer's reusable delivery addresses to the account template.
        $member = Security::getCurrentUser();

        if (!$member || !$member->ID) {
            return ArrayList::create();
        }

        return $member->SavedAddresses()->sort('Title', 'ASC');
    }

    public function EditAddressID(): ?int
    {
        // Pre Saved Addresses Development: keep the selected address open after an edit/save redirect.
        $id = (int) $this->getRequest()->getVar('editAddress');

        return $id > 0 ? $id : null;
    }

    public function saveAccount(HTTPRequest $request): HTTPResponse
    {
        if (!$request->isPOST()) {
            return $this->httpError(405);
        }

        /** @var Member|null $member */
        $member = Security::getCurrentUser();

        if (!$member) {
            $this->setAccountMessage('Unable to update account.');
            return $this->redirectBack();
        }

        $firstName = trim((string)$request->postVar('FirstName'));
        $surname = trim((string)$request->postVar('Surname'));
        $email = trim((string)$request->postVar('Email'));
        $password = (string)$request->postVar('Password');
        $confirmPassword = (string)$request->postVar('ConfirmPassword');

        if ($firstName === '') {
            $this->setAccountMessage('First name is required.');
            return $this->redirectBack();
        }

        if ($surname === '') {
            $this->setAccountMessage('Surname is required.');
            return $this->redirectBack();
        }

        if ($email === '') {
            $this->setAccountMessage('Email address is required.');
            return $this->redirectBack();
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->setAccountMessage('Please enter a valid email address.');
            return $this->redirectBack();
        }

        $existingMember = Member::get()
            ->filter('Email', $email)
            ->exclude('ID', $member->ID)
            ->first();

        if ($existingMember) {
            $this->setAccountMessage('That email address is already in use.');
            return $this->redirectBack();
        }

        if ($password !== '' || $confirmPassword !== '') {
            if ($password === '' || $confirmPassword === '') {
                $this->setAccountMessage('Please complete both password fields.');
                return $this->redirectBack();
            }

            if ($password !== $confirmPassword) {
                $this->setAccountMessage('Passwords do not match.');
                return $this->redirectBack();
            }
        }

        if ($request->postVar('FirstName') !== null) {
            $member->FirstName = $firstName;
        }

        if ($request->postVar('Surname') !== null) {
            $member->Surname = $surname;
        }

        if ($request->postVar('Email') !== null) {
            $member->Email = $email;
        }

        $member->write();

        if ($password !== '' && $confirmPassword !== '') {
            $member->changePassword($password);
        }

        $this->setAccountMessage('Account updated.');
        return $this->redirectBack();
    }

    public function createAddress(HTTPRequest $request): HTTPResponse
    {
        // Pre Saved Addresses Development: create a saved delivery address for the logged-in customer.
        if (!$request->isPOST()) {
            return $this->httpError(405);
        }

        $member = Security::getCurrentUser();

        if (!$member || !$member->ID) {
            $this->setAccountMessage('Unable to create address.');
            return $this->redirectBack();
        }

        [$address, $message] = MemberAddress::createForMemberFromData(
            $member,
            $this->getAddressDataFromRequest($request)
        );

        if ($message !== null) {
            $this->setAccountMessage('Address not created: ' . $message);
            return $this->redirectBack();
        }

        $this->setAccountMessage('Address created.');
        return $this->redirect($this->Link() . '?editAddress=' . $address->ID);
    }

    public function updateAddress(HTTPRequest $request): HTTPResponse
    {
        // Pre Saved Addresses Development: update a saved delivery address owned by the logged-in customer.
        if (!$request->isPOST()) {
            return $this->httpError(405);
        }

        $member = Security::getCurrentUser();
        $id = (int) $request->param('ID');

        if (!$member || !$member->ID) {
            $this->setAccountMessage('Unable to update address.');
            return $this->redirect($this->Link());
        }

        $address = MemberAddress::get()
            ->filter([
                'ID' => $id,
                'MemberID' => $member->ID,
            ])
            ->first();

        if (!$address) {
            $this->setAccountMessage('Address not found.');
            return $this->redirect($this->Link());
        }

        $message = MemberAddress::writeAddressFromData(
            $address,
            $this->getAddressDataFromRequest($request)
        );

        if ($message !== null) {
            $this->setAccountMessage($message);
            return $this->redirect($this->Link() . '?editAddress=' . $id);
        }

        $this->setAccountMessage('Address updated.');
        return $this->redirect($this->Link() . '?editAddress=' . $address->ID);
    }

    public function deleteAddress(HTTPRequest $request): HTTPResponse
    {
        // Pre Saved Addresses Development: delete a saved delivery address owned by the logged-in customer.
        if (!$request->isPOST()) {
            return $this->httpError(405);
        }

        $member = Security::getCurrentUser();
        $id = (int) $request->param('ID');

        if (!$member || !$member->ID) {
            $this->setAccountMessage('Unable to delete address.');
            return $this->redirect($this->Link());
        }

        $address = MemberAddress::get()
            ->filter([
                'ID' => $id,
                'MemberID' => $member->ID,
            ])
            ->first();

        if (!$address) {
            $this->setAccountMessage('Address not found.');
            return $this->redirect($this->Link());
        }

        $address->delete();

        $this->setAccountMessage('Address deleted.');
        return $this->redirect($this->Link());
    }

    protected function getAddressDataFromRequest(HTTPRequest $request): array
    {
        // Pre Saved Addresses Development: collect request values before passing them to the shared MemberAddress helper.
        return [
            'Title' => trim((string) $request->postVar('Title')),
            'DeliveryCompany' => trim((string) $request->postVar('DeliveryCompany')),
            'DeliveryContactName' => trim((string) $request->postVar('DeliveryContactName')),
            'DeliveryPhone' => trim((string) $request->postVar('DeliveryPhone')),
            'DeliveryEmail' => trim((string) $request->postVar('DeliveryEmail')),
            'DeliveryAddressLine1' => trim((string) $request->postVar('DeliveryAddressLine1')),
            'DeliveryAddressLine2' => trim((string) $request->postVar('DeliveryAddressLine2')),
            'DeliveryCity' => trim((string) $request->postVar('DeliveryCity')),
            'DeliveryCounty' => trim((string) $request->postVar('DeliveryCounty')),
            'DeliveryPostcode' => trim((string) $request->postVar('DeliveryPostcode')),
        ];
    }
}
