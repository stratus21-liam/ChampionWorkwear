<?php

namespace ShopModuleDuplicateProducts\Controller;

use Exception;
use ShopModule\Model\CustomerAccount;
use ShopModule\Model\Product;
use ShopModuleDuplicateProducts\Extension\ProductDuplicateExtension;
use ShopModuleDuplicateProducts\Service\ProductDuplicator;
use SilverStripe\Control\Controller;
use SilverStripe\Control\HTTPRequest;
use SilverStripe\ORM\ValidationResult;
use SilverStripe\Security\Permission;
use SilverStripe\Security\Security;
use SilverStripe\Security\SecurityToken;

class ProductDuplicateController extends Controller
{
    private static $allowed_actions = [
        'duplicate' => true,
    ];

    public function duplicate(HTTPRequest $request)
    {
        try {
            if (!$request->isPOST()) {
                throw new Exception('Duplicate requests must be submitted from the CMS form.');
            }

            if (!SecurityToken::inst()->checkRequest($request)) {
                throw new Exception('The duplicate request token was invalid. Please try again.');
            }

            $member = Security::getCurrentUser();

            if (!$member || !Permission::checkMember($member, 'CMS_ACCESS')) {
                throw new Exception('You do not have permission to duplicate products.');
            }

            $productID = (int) $request->param('ProductID');
            $targetAccountID = (int) $request->postVar('DuplicateTargetCustomerAccountID');

            /** @var Product|null $product */
            $product = Product::get()->byID($productID);

            if (!$product || !$product->exists()) {
                throw new Exception('The product could not be found.');
            }

            if (!$product->canEdit($member)) {
                throw new Exception('You do not have permission to edit this product.');
            }

            /** @var CustomerAccount|null $targetAccount */
            $targetAccount = CustomerAccount::get()->byID($targetAccountID);

            if (!$targetAccount || !$targetAccount->exists()) {
                throw new Exception('The target customer account could not be found.');
            }

            if ((int) $targetAccount->ID === (int) $product->CustomerAccountID) {
                throw new Exception('Please choose a different customer account.');
            }

            $duplicator = ProductDuplicator::create();
            $newProduct = $duplicator->duplicate($product, $targetAccount);

            ProductDuplicateExtension::setDuplicateMessage(
                sprintf(
                    'Duplicated "%s" to "%s".',
                    $newProduct->Title,
                    $targetAccount->Title
                ),
                ValidationResult::TYPE_GOOD
            );
        } catch (Exception $exception) {
            ProductDuplicateExtension::setDuplicateMessage(
                $exception->getMessage(),
                ValidationResult::TYPE_ERROR
            );
        }

        return $this->redirectBack();
    }
}
