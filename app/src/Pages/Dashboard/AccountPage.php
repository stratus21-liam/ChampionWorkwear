<?php
namespace App\Pages;

use PageController;
use Page;
use App\Extension\SinglePageInstance;
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
}