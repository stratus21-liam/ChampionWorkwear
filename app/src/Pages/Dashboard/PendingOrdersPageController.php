<?php

namespace App\Pages;

use SilverStripe\Control\HTTPRequest;
use SilverStripe\Control\HTTPResponse;
use SilverStripe\Security\Security;
use PageController;

class PendingOrdersPageController extends PageController
{
    private const MESSAGE_SESSION_KEY = 'Shop.PendingOrdersMessage';

    private static $allowed_actions = [
        'view',
        'approve',
        'reject',
    ];

    private static $url_handlers = [
        '$OrderNumber!/approve' => 'approve',
        '$OrderNumber!/reject' => 'reject',
        '$OrderNumber!' => 'view',
    ];

    public function init()
    {
        parent::init();

        $member = Security::getCurrentUser();

        if ($member) {
            if (!$member->inGroup('administrators')) {
                if (!$this->data()->canAccessPendingOrders($this->CurrentMember())) {
                    $this->httpError(404);
                }
            }
        }
    }

    protected function getMessageSessionKey(): string
    {
        $member = $this->CurrentMember();

        if ($member && $member->ID) {
            return self::MESSAGE_SESSION_KEY . '.Member.' . $member->ID;
        }

        return self::MESSAGE_SESSION_KEY . '.Guest';
    }

    protected function setMessage(string $message): void
    {
        $this->getRequest()
            ->getSession()
            ->set($this->getMessageSessionKey(), $message);
    }

    public function Message(): ?string
    {
        $session = $this->getRequest()->getSession();
        $key = $this->getMessageSessionKey();
        $message = $session->get($key);

        if ($message) {
            $session->clear($key);
        }

        return $message;
    }

    public function view(HTTPRequest $request)
    {
        $item = $this->data()->getPendingOrderForMember(
            $this->CurrentMember(),
            (string) $request->param('OrderNumber')
        );

        if (!$item) {
            return $this->httpError(404);
        }

        return $this->customise([
            'Item' => $item,
            'Order' => $item,
        ])->render();
    }

    public function approve(HTTPRequest $request): HTTPResponse
    {
        if (!$request->isPOST()) {
            return $this->httpError(405);
        }

        $order = $this->data()->approvePendingOrderForMember(
            $this->CurrentMember(),
            (string) $request->param('OrderNumber')
        );

        if (!$order) {
            return $this->httpError(404);
        }

        $this->setMessage(sprintf(
            'Order %s has been approved.',
            $order->OrderNumber
        ));

        return $this->redirect($this->Link());
    }

    public function reject(HTTPRequest $request): HTTPResponse
    {
        if (!$request->isPOST()) {
            return $this->httpError(405);
        }

        $reason = trim((string) $request->postVar('RejectionReason'));

        if ($reason === '') {
            $this->setMessage('Please provide a rejection reason.');
            return $this->redirectBack();
        }

        $order = $this->data()->rejectPendingOrderForMember(
            $this->CurrentMember(),
            (string) $request->param('OrderNumber'),
            $reason
        );

        if (!$order) {
            return $this->httpError(404);
        }

        $this->setMessage(sprintf(
            'Order %s has been rejected.',
            $order->OrderNumber
        ));

        return $this->redirect($this->Link());
    }
}