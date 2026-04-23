<?php

namespace App\Pages;

use App\Email\Mailer;
use App\Model\Order;
use App\Model\OrderItem;
use PageController;
use ShopModule\Model\MemberAddress;
use SilverStripe\Control\HTTPRequest;
use SilverStripe\Control\HTTPResponse;
use SilverStripe\ORM\ArrayList;
use SilverStripe\ORM\DB;
use SilverStripe\Security\Security;

class CheckoutPageController extends PageController
{
    private const CHECKOUT_MESSAGE_SESSION_KEY = 'Shop.CheckoutMessage';
    private const CHECKOUT_ORDER_SESSION_KEY = 'Shop.CheckoutSubmittedOrder';

    private static $allowed_actions = [
        'placeOrder',
    ];

    protected function getCheckoutMessageSessionKey(): string
    {
        $member = Security::getCurrentUser();

        if ($member && $member->ID) {
            return self::CHECKOUT_MESSAGE_SESSION_KEY . '.Member.' . $member->ID;
        }

        return self::CHECKOUT_MESSAGE_SESSION_KEY . '.Guest';
    }

    protected function getCheckoutOrderSessionKey(): string
    {
        $member = Security::getCurrentUser();

        if ($member && $member->ID) {
            return self::CHECKOUT_ORDER_SESSION_KEY . '.Member.' . $member->ID;
        }

        return self::CHECKOUT_ORDER_SESSION_KEY . '.Guest';
    }

    protected function setCheckoutMessage(string $message): void
    {
        $this->getRequest()
            ->getSession()
            ->set($this->getCheckoutMessageSessionKey(), $message);
    }

    public function CheckoutMessage(): ?string
    {
        $session = $this->getRequest()->getSession();
        $messageKey = $this->getCheckoutMessageSessionKey();
        $message = $session->get($messageKey);

        if ($message) {
            $session->clear($messageKey);
        }

        return $message;
    }

    protected function setSubmittedOrderID(int $orderID): void
    {
        $this->getRequest()
            ->getSession()
            ->set($this->getCheckoutOrderSessionKey(), $orderID);
    }

    protected function clearSubmittedOrderID(): void
    {
        $this->getRequest()
            ->getSession()
            ->clear($this->getCheckoutOrderSessionKey());
    }

    public function SubmittedOrder(): ?Order
    {
        // If the user has started a new cart, show checkout normally again
        if ($this->CartCount() > 0) {
            return null;
        }

        $orderID = (int) $this->getRequest()
            ->getSession()
            ->get($this->getCheckoutOrderSessionKey());

        if (!$orderID) {
            return null;
        }

        $order = Order::get()->byID($orderID);

        if (!$order || !$order->exists()) {
            $this->clearSubmittedOrderID();
            return null;
        }

        return $order;
    }

    public function HasSubmittedOrderConfirmation(): bool
    {
        return (bool) $this->SubmittedOrder();
    }

    public function SavedAddresses()
    {
        // Pre Saved Addresses Development: expose the logged-in customer's reusable delivery addresses to checkout.
        $member = Security::getCurrentUser();

        if (!$member || !$member->ID) {
            return ArrayList::create();
        }

        return $member->SavedAddresses()->sort('Title', 'ASC');
    }

    protected function getSavedAddressDataFromCheckout(HTTPRequest $request, array $orderData): array
    {
        // Pre Saved Addresses Development: convert checkout delivery fields into the shared MemberAddress helper format.
        return [
            'Title' => trim((string) $request->postVar('SavedDeliveryAddressTitle')),
            'DeliveryCompany' => $orderData['DeliveryCompany'] ?? '',
            'DeliveryContactName' => $orderData['DeliveryContactName'] ?? '',
            'DeliveryPhone' => $orderData['DeliveryPhone'] ?? '',
            'DeliveryEmail' => $orderData['DeliveryEmail'] ?? '',
            'DeliveryAddressLine1' => $orderData['DeliveryAddressLine1'] ?? '',
            'DeliveryAddressLine2' => $orderData['DeliveryAddressLine2'] ?? '',
            'DeliveryCity' => $orderData['DeliveryCity'] ?? '',
            'DeliveryCounty' => $orderData['DeliveryCounty'] ?? '',
            'DeliveryPostcode' => $orderData['DeliveryPostcode'] ?? '',
        ];
    }

    public function placeOrder(HTTPRequest $request): HTTPResponse
    {
        if (!$request->isPOST()) {
            return $this->httpError(405);
        }

        $member = Security::getCurrentUser();

        if (!$member || !$member->ID) {
            $this->setCheckoutMessage('You must be logged in to place an order.');
            return $this->redirectBack();
        }

        $cart = $this->getCartSession();

        if (empty($cart)) {
            $this->setCheckoutMessage('Your cart is empty.');
            return $this->redirectBack();
        }

        $fulfilmentMethod = trim((string) $request->postVar('FulfilmentMethod'));
        $poNumber = trim((string) $request->postVar('PONumber'));
        $orderNotes = trim((string) $request->postVar('OrderNotes'));
        $savedAddressID = (int) $request->postVar('SavedDeliveryAddressID');
        $saveDeliveryAddress = (bool) $request->postVar('SaveDeliveryAddress');

        $data = [
            'FulfilmentMethod' => $fulfilmentMethod,
            'PONumber' => $poNumber,
            'OrderNotes' => $orderNotes,
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

        // Pre Saved Addresses Development: if a saved address is selected, use it as a trusted fallback for required delivery fields.
        if ($fulfilmentMethod === 'delivery') {
            if ($savedAddressID) {
                $savedAddress = MemberAddress::get()
                    ->filter([
                        'ID' => $savedAddressID,
                        'MemberID' => $member->ID,
                    ])
                    ->first();

                if (!$savedAddress) {
                    $this->setCheckoutMessage('Selected delivery address was not found.');
                    return $this->redirectBack();
                }

                foreach ([
                    'DeliveryCompany',
                    'DeliveryContactName',
                    'DeliveryPhone',
                    'DeliveryEmail',
                    'DeliveryAddressLine1',
                    'DeliveryAddressLine2',
                    'DeliveryCity',
                    'DeliveryCounty',
                    'DeliveryPostcode',
                ] as $field) {
                    if ($data[$field] === '') {
                        $data[$field] = (string) $savedAddress->$field;
                    }
                }
            }
        }

        if ($fulfilmentMethod === 'delivery' && $saveDeliveryAddress && $savedAddressID) {
            // Pre Saved Addresses Development: only brand-new checkout addresses can be saved.
            $this->setCheckoutMessage('Please enter a new delivery address before saving it to your account.');
            return $this->redirectBack();
        }

        if (!in_array($fulfilmentMethod, ['collection', 'delivery'], true)) {
            $this->setCheckoutMessage('Please select collection or delivery.');
            return $this->redirectBack();
        }

        if ($fulfilmentMethod === 'delivery') {
            $requiredDeliveryFields = [
                'DeliveryCompany' => 'Company name',
                'DeliveryContactName' => 'Contact name',
                'DeliveryPhone' => 'Phone',
                'DeliveryEmail' => 'Email',
                'DeliveryAddressLine1' => 'Address line 1',
                'DeliveryCity' => 'Town / City',
                'DeliveryCounty' => 'County',
                'DeliveryPostcode' => 'Postcode',
            ];

            foreach ($requiredDeliveryFields as $field => $label) {
                if ($data[$field] === '') {
                    $this->setCheckoutMessage($label . ' is required for delivery.');
                    return $this->redirectBack();
                }
            }

            if (!filter_var($data['DeliveryEmail'], FILTER_VALIDATE_EMAIL)) {
                $this->setCheckoutMessage('Please enter a valid delivery email address.');
                return $this->redirectBack();
            }

            if ($saveDeliveryAddress && !$savedAddressID) {
                // Pre Saved Addresses Development: validate the new saved address before the order transaction starts.
                $savedAddressData = $this->getSavedAddressDataFromCheckout($request, $data);
                $savedAddressMessage = MemberAddress::validateAddressData($savedAddressData);

                if ($savedAddressMessage !== null) {
                    $this->setCheckoutMessage('Address not saved: ' . $savedAddressMessage);
                    return $this->redirectBack();
                }
            }
        }

        $cartTotal = (float) $this->CartTotal()->getValue();

        if ((bool) $member->EnableSpendLimit) {
            $spendLimit = (float) $member->SpendLimit;

            if ($cartTotal > $spendLimit) {
                $this->setCheckoutMessage(
                    sprintf(
                        'This order exceeds your spend limit of £%0.2f.',
                        $spendLimit
                    )
                );
                return $this->redirectBack();
            }
        }

        $requiresApproval = (bool) $member->RequiresApproval;
        $status = $requiresApproval
            ? Order::STATUS_PENDING_APPROVAL
            : Order::STATUS_APPROVED;

        try {
            $order = null;

            DB::get_conn()->withTransaction(function () use (
                &$order,
                $member,
                $data,
                $status,
                $requiresApproval,
                $cart,
                $cartTotal,
                $saveDeliveryAddress,
                $savedAddressID,
                $request
            ) {
                $order = Order::create();
                $order->CustomerID = (int) $member->ID;
                $order->CustomerAccountID = (int) $member->CustomerAccountID;
                $order->Status = $status;
                $order->RequiresApproval = $requiresApproval;
                $order->Subtotal = $cartTotal;
                $order->Total = $cartTotal;

                foreach ($data as $field => $value) {
                    $order->$field = $value;
                }

                $order->write();

                foreach ($cart as $cartItem) {
                    $qty = max(1, (int) ($cartItem['qty'] ?? 1));
                    $unitPrice = (float) ($cartItem['price'] ?? 0);

                    $item = OrderItem::create();
                    $item->OrderID = (int) $order->ID;
                    $item->ProductID = !empty($cartItem['product_id']) ? (int) $cartItem['product_id'] : 0;
                    $item->ProductTitle = (string) ($cartItem['title'] ?? '');
                    $item->SKU = (string) ($cartItem['sku'] ?? '');
                    $item->Quantity = $qty;
                    $item->UnitPrice = $unitPrice;
                    $item->OptionsJSON = json_encode($cartItem['attributes'] ?? []);
                    $item->write();
                }

                $order->Subtotal = $order->getItemsSubtotal();
                $order->Total = $order->Subtotal;
                $order->write();

                if ($data['FulfilmentMethod'] === 'delivery' && $saveDeliveryAddress && !$savedAddressID) {
                    // Pre Saved Addresses Development: save new checkout addresses only after the order itself has been created.
                    [$savedCheckoutAddress, $savedCheckoutAddressMessage] = MemberAddress::createForMemberFromData(
                        $member,
                        $this->getSavedAddressDataFromCheckout($request, $data)
                    );

                    if ($savedCheckoutAddressMessage !== null) {
                        throw new \RuntimeException($savedCheckoutAddressMessage);
                    }
                }
            });

            if (!$order || !$order->exists()) {
                $this->setCheckoutMessage('There was a problem creating your order.');
                return $this->redirectBack();
            }

            $this->getRequest()
                ->getSession()
                ->clear($this->getCartSessionKey());

            $this->setSubmittedOrderID((int) $order->ID);

            Mailer::sendOrderSubmittedToCustomer($order);

            if ($order->RequiresApproval) {
                Mailer::sendOrderApprovalRequestToCustomerAccountAdmins($order);

                $this->setCheckoutMessage(sprintf(
                    'Order %s has been submitted and is awaiting approval.',
                    $order->OrderNumber
                ));
            } else {

                // Do not email Champion if requires approval - only email champion when it needs processing
                Mailer::sendOrderSubmittedToChampion($order);

                $this->setCheckoutMessage(sprintf(
                    'Order %s has been submitted and is now being processed.',
                    $order->OrderNumber
                ));
            }
        } catch (\Throwable $e) {
            // $this->setCheckoutMessage($e);
            $this->setCheckoutMessage('There was a problem creating your order. Please try again.');
        }

        return $this->redirectBack();
    }
}
