<?php
namespace App\Pages;

use PageController;
use Page;
use App\Extension\SinglePageInstance;
use SilverStripe\Security\Security;
use SilverStripe\Security\Member;
use SilverStripe\Control\HTTPRequest;
use SilverStripe\Control\HTTPResponse;
use SilverStripe\ORM\ArrayList;
use ShopModule\Model\Role;

class ManageUsersPage extends Page
{
    private static $singular_name = "Manage Users Page";

    private static $icon_class = 'font-icon-torsos-all';

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

    public function canManageUsers(): bool
    {
        $member = Security::getCurrentUser();
        return $member && $member->IsAdmin;
    }

    public function AvailableRoles()
    {
        $member = Security::getCurrentUser();

        if (!$member || !$member->CustomerAccountID) {
            return ArrayList::create();
        }

        return Role::get()
            ->filter('CustomerAccountID', $member->CustomerAccountID)
            ->sort('Title', 'ASC');
    }

    public function ManagedUsers()
    {
        $member = Security::getCurrentUser();

        if (!$member || !$member->CustomerAccountID) {
            return ArrayList::create();
        }

        return Member::get()
            ->filter('CustomerAccountID', $member->CustomerAccountID)
            ->sort('FirstName', 'ASC');
    }
}

class ManageUsersPageController extends PageController
{
    private const DASHBOARD_MESSAGE_SESSION_KEY = 'Shop.DashboardMessage';
    private const CREATE_FORM_DATA_SESSION_KEY = 'Shop.ManageUsers.CreateFormData';

    private static $allowed_actions = [
        'createUser',
        'updateUser',
        'deleteUser',
    ];

    public function init()
    {
        parent::init();
        $member = Security::getCurrentUser();

        if ($member) {
            if (!$member->inGroup('administrators')) {
                if (!$this->dataRecord || !$this->dataRecord->canManageUsers()) {
                    return $this->httpError(403);
                }
            }
        }
    }

    public function EditUserID(): ?int
    {
        $id = (int)$this->getRequest()->getVar('editUser');
        return $id > 0 ? $id : null;
    }

    public function createUser(HTTPRequest $request): HTTPResponse
    {
        if (!$request->isPOST()) {
            return $this->httpError(405);
        }

        $currentMember = Security::getCurrentUser();

        if (!$currentMember || !$currentMember->CustomerAccountID) {
            $this->setDashboardMessage('Unable to create user.');
            return $this->redirectBack();
        }

        $this->setCreateFormData([
            'FirstName' => trim((string)$request->postVar('FirstName')),
            'Surname' => trim((string)$request->postVar('Surname')),
            'Email' => trim((string)$request->postVar('Email')),
            'RoleID' => (int)$request->postVar('RoleID'),
            'EnableSpendLimit' => (bool)$request->postVar('EnableSpendLimit'),
            'SpendLimit' => trim((string)$request->postVar('SpendLimit')),
            'IsAdmin' => (bool)$request->postVar('IsAdmin'),
            'RequiresApproval' => (bool)$request->postVar('RequiresApproval'),
        ]);

        $firstName = trim((string)$request->postVar('FirstName'));
        $surname = trim((string)$request->postVar('Surname'));
        $email = trim((string)$request->postVar('Email'));

        if (!$firstName) {
            $this->setDashboardMessage('User not created: First name is required.');
            return $this->redirectBack();
        }

        if (!$surname) {
            $this->setDashboardMessage('User not created: Surname is required.');
            return $this->redirectBack();
        }

        if (!$email) {
            $this->setDashboardMessage('User not created: Email is required.');
            return $this->redirectBack();
        }

        $existingMember = Member::get()->filter('Email', $email)->first();
        if ($existingMember) {
            $this->setDashboardMessage('User not created: A user with this email already exists.');
            return $this->redirectBack();
        }

        $password = (string)$request->postVar('Password');
        $confirmPassword = (string)$request->postVar('ConfirmPassword');

        if (!$password || !$confirmPassword) {
            $this->setDashboardMessage('User not created: Password and confirm password are required.');
            return $this->redirectBack();
        }

        if ($password !== $confirmPassword) {
            $this->setDashboardMessage('User not created: Passwords do not match.');
            return $this->redirectBack();
        }

        $isAdmin = (bool)$request->postVar('IsAdmin');
        $roleID = (int)$request->postVar('RoleID');

        if (!$isAdmin && !$roleID) {
            $this->setDashboardMessage('User not created: Role is required unless the user is an account admin.');
            return $this->redirectBack();
        }

        $enableSpendLimit = (bool)$request->postVar('EnableSpendLimit');
        $spendLimitRaw = trim((string)$request->postVar('SpendLimit'));
        $spendLimit = 0;

        if ($enableSpendLimit) {
            if ($spendLimitRaw === '' || !preg_match('/^\d+(\.\d{1,2})?$/', $spendLimitRaw)) {
                $this->setDashboardMessage('User not created: Spend limit must be a valid currency amount.');
                return $this->redirectBack();
            }

            $spendLimit = (float)$spendLimitRaw;

            if ($spendLimit <= 0) {
                $this->setDashboardMessage('User not created: Spend limit must be greater than 0 if enabled.');
                return $this->redirectBack();
            }
        }

        $member = Member::create();
        $member->FirstName = $firstName;
        $member->Surname = $surname;
        $member->Email = $email;
        $member->CustomerAccountID = $currentMember->CustomerAccountID;

        if ($roleID) {
            $member->RoleID = $roleID;
        }

        $member->IsAdmin = $isAdmin;
        $member->Active = true;
        $member->EnableSpendLimit = $enableSpendLimit;
        $member->SpendLimit = $enableSpendLimit ? $spendLimit : 0;
        $member->RequiresApproval = (bool)$request->postVar('RequiresApproval');

        $member->write();

        $member->changePassword($password);

        if ($member->hasField('PasswordExpiry')) {
            $member->PasswordExpiry = date('Y-m-d H:i:s', strtotime('-1 day'));
            $member->write();
        }

        $this->clearCreateFormData();
        $this->setDashboardMessage('User created.');

        return $this->redirect($this->Link() . '?editUser=' . $member->ID);
    }

    public function updateUser(HTTPRequest $request): HTTPResponse
    {
        if (!$request->isPOST()) {
            return $this->httpError(405);
        }

        $currentMember = Security::getCurrentUser();
        $id = (int)$request->param('ID');

        if (!$currentMember || !$currentMember->CustomerAccountID) {
            $this->setDashboardMessage('Unable to update user.');
            return $this->redirect($this->Link());
        }

        $member = Member::get()
            ->filter([
                'ID' => $id,
                'CustomerAccountID' => $currentMember->CustomerAccountID
            ])
            ->first();

        if (!$member) {
            $this->setDashboardMessage('User not found.');
            return $this->redirect($this->Link());
        }

        if ((int)$member->ID === (int)$currentMember->ID) {
            $this->setDashboardMessage('You cannot edit your own user record from Manage Users.');
            return $this->redirect($this->Link() . '?editUser=' . $id);
        }

        $firstName = trim((string)$request->postVar('FirstName'));
        $surname = trim((string)$request->postVar('Surname'));
        $email = trim((string)$request->postVar('Email'));

        if ($firstName === '') {
            $this->setDashboardMessage('First name is required.');
            return $this->redirect($this->Link() . '?editUser=' . $id);
        }

        if ($surname === '') {
            $this->setDashboardMessage('Surname is required.');
            return $this->redirect($this->Link() . '?editUser=' . $id);
        }

        if ($email === '') {
            $this->setDashboardMessage('Email address is required.');
            return $this->redirect($this->Link() . '?editUser=' . $id);
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->setDashboardMessage('Please enter a valid email address.');
            return $this->redirect($this->Link() . '?editUser=' . $id);
        }

        $existingMember = Member::get()
            ->filter('Email', $email)
            ->exclude('ID', $member->ID)
            ->first();

        if ($existingMember) {
            $this->setDashboardMessage('That email address is already in use.');
            return $this->redirect($this->Link() . '?editUser=' . $id);
        }

        $enableSpendLimit = (bool)$request->postVar('EnableSpendLimit');
        $spendLimitRaw = trim((string)$request->postVar('SpendLimit'));
        $spendLimit = 0;

        if ($enableSpendLimit) {
            if ($spendLimitRaw === '' || !preg_match('/^\d+(\.\d{1,2})?$/', $spendLimitRaw)) {
                $this->setDashboardMessage('Spend limit must be a valid currency amount.');
                return $this->redirect($this->Link() . '?editUser=' . $id);
            }

            $spendLimit = (float)$spendLimitRaw;

            if ($spendLimit <= 0) {
                $this->setDashboardMessage('Spend limit must be greater than 0 if enabled.');
                return $this->redirect($this->Link() . '?editUser=' . $id);
            }
        }

        $roleID = (int)$request->postVar('RoleID');
        if ($roleID) {
            $member->RoleID = $roleID;
        }

        $member->FirstName = $firstName;
        $member->Surname = $surname;
        $member->Email = $email;
        $member->IsAdmin = (bool)$request->postVar('IsAdmin');
        $member->Active = (bool)$request->postVar('Active');
        $member->EnableSpendLimit = $enableSpendLimit;
        $member->SpendLimit = $enableSpendLimit ? $spendLimit : 0;
        $member->RequiresApproval = (bool)$request->postVar('RequiresApproval');
        $member->write();

        $this->setDashboardMessage('User updated.');
        return $this->redirect($this->Link() . '?editUser=' . $member->ID);
    }

    public function deleteUser(HTTPRequest $request): HTTPResponse
    {
        if (!$request->isPOST()) {
            return $this->httpError(405);
        }

        $currentMember = Security::getCurrentUser();
        $id = (int)$request->param('ID');

        $member = Member::get()
            ->filter([
                'ID' => $id,
                'CustomerAccountID' => $currentMember->CustomerAccountID
            ])
            ->first();

        if (!$member) {
            $this->setDashboardMessage('User not found.');
            return $this->redirectBack();
        }

        if ((int)$member->ID === (int)$currentMember->ID) {
            $this->setDashboardMessage('You cannot delete your own account.');
            return $this->redirectBack();
        }

        $member->delete();

        $this->setDashboardMessage('User deleted.');
        return $this->redirect($this->Link());
    }

    public function CreateFormDataValue(string $field)
    {
        $data = $this->getCreateFormData();

        if (!is_array($data) || !array_key_exists($field, $data)) {
            return null;
        }

        return $data[$field];
    }

    public function CreateFormChecked(string $field): bool
    {
        return (bool)$this->CreateFormDataValue($field);
    }

    protected function getDashboardMessageSessionKey(): string
    {
        $member = Security::getCurrentUser();

        if ($member && $member->ID) {
            return self::DASHBOARD_MESSAGE_SESSION_KEY . '.Member.' . $member->ID;
        }

        return self::DASHBOARD_MESSAGE_SESSION_KEY . '.Guest';
    }

    protected function getCreateFormDataSessionKey(): string
    {
        $member = Security::getCurrentUser();

        if ($member && $member->ID) {
            return self::CREATE_FORM_DATA_SESSION_KEY . '.Member.' . $member->ID;
        }

        return self::CREATE_FORM_DATA_SESSION_KEY . '.Guest';
    }

    protected function setDashboardMessage(string $message): void
    {
        $this->getRequest()
            ->getSession()
            ->set($this->getDashboardMessageSessionKey(), $message);
    }

    public function DashboardMessage(): ?string
    {
        $session = $this->getRequest()->getSession();
        $messageKey = $this->getDashboardMessageSessionKey();
        $message = $session->get($messageKey);

        if ($message) {
            $session->clear($messageKey);
        }

        return $message;
    }

    protected function setCreateFormData(array $data): void
    {
        $this->getRequest()
            ->getSession()
            ->set($this->getCreateFormDataSessionKey(), $data);
    }

    protected function getCreateFormData(): array
    {
        $data = $this->getRequest()
            ->getSession()
            ->get($this->getCreateFormDataSessionKey());

        return is_array($data) ? $data : [];
    }

    protected function clearCreateFormData(): void
    {
        $this->getRequest()
            ->getSession()
            ->clear($this->getCreateFormDataSessionKey());
    }
}
