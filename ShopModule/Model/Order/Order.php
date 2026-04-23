<?php

namespace App\Model;

use App\Extension\Sluggable;
use ShopModule\Model\CustomerAccount;
use SilverStripe\Control\Controller;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\GridField\GridField;
use SilverStripe\Forms\GridField\GridFieldConfig_RecordViewer;
use SilverStripe\Forms\HeaderField;
use SilverStripe\Forms\LiteralField;
use SilverStripe\Forms\ReadonlyField;
use SilverStripe\ORM\DataObject;
use SilverStripe\ORM\FieldType\DBDatetime;
use SilverStripe\ORM\ValidationResult;
use SilverStripe\Security\Member;
use SilverStripe\Security\Permission;
use App\Pages\OrderHistoryPage;
use App\Pages\PendingOrdersPage;

class Order extends DataObject
{
    public const STATUS_PENDING_APPROVAL = 'PendingApproval';
    public const STATUS_SUBMITTED = 'Submitted';
    public const STATUS_APPROVED = 'Approved';
    public const STATUS_REJECTED = 'Rejected';
    public const STATUS_CANCELLED = 'Cancelled';

    private static $table_name = 'ShopOrder';

    private static $db = [
        'OrderNumber' => 'Varchar(50)',
        'Status' => 'Varchar(50)',
        'RequiresApproval' => 'Boolean',

        'SubmittedAt' => 'Datetime',
        'ApprovedAt' => 'Datetime',
        'RejectedAt' => 'Datetime',
        'RejectionReason' => 'Text',

        'FulfilmentMethod' => 'Varchar(20)',
        'PONumber' => 'Varchar(255)',
        'OrderNotes' => 'Text',

        'DeliveryCompany' => 'Varchar(255)',
        'DeliveryContactName' => 'Varchar(255)',
        'DeliveryPhone' => 'Varchar(50)',
        'DeliveryEmail' => 'Varchar(255)',
        'DeliveryAddressLine1' => 'Varchar(255)',
        'DeliveryAddressLine2' => 'Varchar(255)',
        'DeliveryCity' => 'Varchar(255)',
        'DeliveryCounty' => 'Varchar(255)',
        'DeliveryPostcode' => 'Varchar(30)',

        'Subtotal' => 'Currency',
        'Total' => 'Currency',
    ];

    private static $has_one = [
        'Customer' => Member::class,
        'CustomerAccount' => CustomerAccount::class,
        'ApprovedBy' => Member::class,
    ];

    private static $has_many = [
        'Items' => OrderItem::class,
    ];

    private static $cascade_deletes = [
        'Items',
    ];

    private static $default_sort = 'Created DESC';

    private static $summary_fields = [
        'OrderNumber' => 'Order Number',
        'CustomerSummary' => 'Customer',
        'CustomerAccount.Title' => 'Customer Account',
        'Status' => 'Status',
        'FulfilmentMethodNice' => 'Fulfilment',
        'Total.Nice' => 'Total',
        'SubmittedAt.Nice' => 'Submitted'
    ];

    private static $searchable_fields = [
        'OrderNumber',
        'Status',
        'Customer.FirstName',
        'Customer.Surname',
        'Customer.Email',
        'CustomerAccountID' => ['title' => 'Customer Account' ],
        'CustomerID' => ['title' => 'Customer' ],
        'PONumber',
    ];

    public function populateDefaults()
    {
        parent::populateDefaults();

        if (!$this->Status) {
            $this->Status = self::STATUS_SUBMITTED;
        }
    }

    public function onBeforeWrite()
    {
        parent::onBeforeWrite();

        if (!$this->OrderNumber) {
            $this->OrderNumber = $this->generateOrderNumber();
        }

        if (!$this->SubmittedAt && !$this->isInDB()) {
            $this->SubmittedAt = DBDatetime::now()->Rfc2822();
        }

        if ($this->isInDB()) {
            $this->Subtotal = $this->getItemsSubtotal();
            $this->Total = $this->Subtotal;
        }
    }

    public function validate()
    {
        $result = parent::validate();

        if (!in_array($this->FulfilmentMethod, ['collection', 'delivery'], true)) {
            $result->addError('Fulfilment method must be collection or delivery.');
        }

        if ($this->FulfilmentMethod === 'delivery') {
            $required = [
                'DeliveryCompany' => 'Delivery company is required.',
                'DeliveryContactName' => 'Delivery contact name is required.',
                'DeliveryPhone' => 'Delivery phone is required.',
                'DeliveryEmail' => 'Delivery email is required.',
                'DeliveryAddressLine1' => 'Delivery address line 1 is required.',
                'DeliveryCity' => 'Delivery city is required.',
                'DeliveryCounty' => 'Delivery county is required.',
                'DeliveryPostcode' => 'Delivery postcode is required.',
            ];

            foreach ($required as $field => $message) {
                if (trim((string) $this->$field) === '') {
                    $result->addError($message);
                }
            }

            if ($this->DeliveryEmail && !filter_var($this->DeliveryEmail, FILTER_VALIDATE_EMAIL)) {
                $result->addError('Delivery email must be a valid email address.');
            }
        }

        return $result;
    }

    public function getCMSFields()
    {
        $fields = FieldList::create();

        $fields->push(HeaderField::create('OrderHeader', 'Order details'));
        $fields->push(ReadonlyField::create('OrderNumber', 'Order number', $this->OrderNumber));
        $fields->push(ReadonlyField::create('Status', 'Status', $this->Status));
        $fields->push(ReadonlyField::create('RequiresApprovalNice', 'Requires approval', $this->getRequiresApprovalNice()));
        if($this->ApprovedByID){
            $fields->push(ReadonlyField::create('ApprovedByNice', 'Approved By', $this->getApprovedByNice()));
            $fields->push(ReadonlyField::create('SubmittedAtNice', 'Submitted', $this->dbObject('SubmittedAt')->Nice()));
            $fields->push(ReadonlyField::create('ApprovedAtNice', 'Approved', $this->ApprovedAt ? $this->dbObject('ApprovedAt')->Nice() : ''));
            $fields->push(ReadonlyField::create('RejectedAtNice', 'Rejected', $this->RejectedAt ? $this->dbObject('RejectedAt')->Nice() : ''));
            $fields->push(ReadonlyField::create('RejectionReason', 'Rejection reason', $this->RejectionReason));            
        }

        $fields->push(HeaderField::create('CustomerHeader', 'Customer'));
        $fields->push(ReadonlyField::create('CustomerSummary', 'Customer', $this->getCustomerSummary()));
        $fields->push(ReadonlyField::create('CustomerEmail', 'Customer email', $this->Customer()->exists() ? $this->Customer()->Email : ''));
        $fields->push(ReadonlyField::create('CustomerAccountTitle', 'Customer account', $this->CustomerAccount()->exists() ? $this->CustomerAccount()->Title : ''));

        $fields->push(HeaderField::create('OrderInfoHeader', 'Order information'));
        $fields->push(ReadonlyField::create('FulfilmentMethodNice', 'Fulfilment method', $this->getFulfilmentMethodNice()));
        $fields->push(ReadonlyField::create('PONumber', 'PO number', $this->PONumber));
        $fields->push(ReadonlyField::create('OrderNotes', 'Order notes', $this->OrderNotes));
        $fields->push(ReadonlyField::create('SubtotalNice', 'Subtotal', $this->dbObject('Subtotal')->Nice()));
        $fields->push(ReadonlyField::create('TotalNice', 'Total', $this->dbObject('Total')->Nice()));

        if ($this->getIsDelivery()) {
            $fields->push(HeaderField::create('DeliveryHeader', 'Delivery details'));
            $fields->push(ReadonlyField::create('DeliveryCompany', 'Company', $this->DeliveryCompany));
            $fields->push(ReadonlyField::create('DeliveryContactName', 'Contact name', $this->DeliveryContactName));
            $fields->push(ReadonlyField::create('DeliveryPhone', 'Phone', $this->DeliveryPhone));
            $fields->push(ReadonlyField::create('DeliveryEmail', 'Email', $this->DeliveryEmail));
            $fields->push(ReadonlyField::create('DeliveryAddressLine1', 'Address line 1', $this->DeliveryAddressLine1));
            $fields->push(ReadonlyField::create('DeliveryAddressLine2', 'Address line 2', $this->DeliveryAddressLine2));
            $fields->push(ReadonlyField::create('DeliveryCity', 'Town / City', $this->DeliveryCity));
            $fields->push(ReadonlyField::create('DeliveryCounty', 'County', $this->DeliveryCounty));
            $fields->push(ReadonlyField::create('DeliveryPostcode', 'Postcode', $this->DeliveryPostcode));
        } else {
            $fields->push(HeaderField::create('CollectionHeader', 'Collection'));
            $fields->push(LiteralField::create(
                'CollectionMessage',
                '<p>This order is for collection from the main office.</p>'
            ));
        }

        if ($this->isInDB()) {
            $fields->push(
                GridField::create(
                    'Items',
                    'Order items',
                    $this->Items(),
                    GridFieldConfig_RecordViewer::create()
                )
            );
        }

        return $fields;
    }

    public function getItemsSubtotal(): float
    {
        $total = 0.0;

        foreach ($this->Items() as $item) {
            $total += (float) $item->LineTotal;
        }

        return $total;
    }

    public function getTotalQuantity(): int
    {
        $qty = 0;

        foreach ($this->Items() as $item) {
            $qty += (int) $item->Quantity;
        }

        return $qty;
    }

    public function getFulfilmentMethodNice(): string
    {
        return ucfirst((string) $this->FulfilmentMethod);
    }

    public function getStatusNice(): string
    {
        switch ((string) $this->Status) {
            case self::STATUS_PENDING_APPROVAL:
                return 'Pending Approval';

            case self::STATUS_APPROVED:
                return 'Processing';

            case self::STATUS_SUBMITTED:
                return 'Submitted';

            case self::STATUS_REJECTED:
                return 'Rejected';

            case self::STATUS_CANCELLED:
                return 'Cancelled';

            default:
                return (string) $this->Status;
        }
    }

    public function getIsDelivery(): bool
    {
        return $this->FulfilmentMethod === 'delivery';
    }

    public function getIsCollection(): bool
    {
        return $this->FulfilmentMethod === 'collection';
    }

    public function getRequiresApprovalNice(): string
    {
        return $this->RequiresApproval ? 'Yes' : 'No';
    }

    public function getApprovedByNice(): string
    {
        $member = $this->ApprovedBy();

        if (!$member || !$member->exists()) {
            return '';
        }

        $name = trim(implode(' ', array_filter([
            (string) $member->FirstName,
            (string) $member->Surname,
        ])));

        if ($name !== '') {
            return $member->Email ? $name . ' (' . $member->Email . ')' : $name;
        }

        return (string) $member->Email;
    }

    public function getCustomerSummary(): string
    {
        if (!$this->Customer()->exists()) {
            return '';
        }

        $name = trim((string) $this->Customer()->Name);

        if ($name !== '') {
            return $name;
        }

        return (string) $this->Customer()->Email;
    }

    protected function generateOrderNumber(): string
    {
        do {
            $number = 'CW-' . date('Ymd-His') . '-' . random_int(1000, 9999);

            $exists = self::get()
                ->filter('OrderNumber', $number)
                ->exists();

        } while ($exists);

        return $number;
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

    /**
     * Link for customer order history view
     */
    public function OrderHistoryLink($action = null)
    {
        if ($page = OrderHistoryPage::get()->first()) {
            return Controller::join_links(
                $page->Link(),
                $this->OrderNumber,
                $action
            );
        }

        return null;
    }

    /**
     * Link for pending orders (admin approval view)
     */
    public function PendingOrderLink($action = null)
    {
        if ($page = PendingOrdersPage::get()->first()) {
            return Controller::join_links(
                $page->Link(),
                $this->OrderNumber,
                $action
            );
        }

        return null;
    }  
}