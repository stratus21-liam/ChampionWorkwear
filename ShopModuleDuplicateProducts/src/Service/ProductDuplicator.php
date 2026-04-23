<?php

namespace ShopModuleDuplicateProducts\Service;

use ShopModule\Model\CustomerAccount;
use ShopModule\Model\Product;
use RuntimeException;
use SilverStripe\Core\Injector\Injectable;
use SilverStripe\ORM\DB;

class ProductDuplicator
{
    use Injectable;

    public function duplicate(Product $sourceProduct, CustomerAccount $targetAccount): Product
    {
        $duplicatedProduct = null;

        DB::get_conn()->withTransaction(function () use ($sourceProduct, $targetAccount, &$duplicatedProduct) {
            $newProduct = Product::create();
            $newProduct->Title = $sourceProduct->Title;
            $newProduct->Description = $sourceProduct->Description;
            $newProduct->SKU = $this->getUniqueSKU($sourceProduct->SKU, (int) $targetAccount->ID);
            $newProduct->Price = $sourceProduct->Price;
            $newProduct->Active = $sourceProduct->Active;
            $newProduct->FeaturedImageID = $sourceProduct->FeaturedImageID;
            $newProduct->CustomerAccountID = (int) $targetAccount->ID;
            $newProduct->write();

            foreach ($sourceProduct->Images() as $image) {
                $newProduct->Images()->add($image, [
                    'SortOrder' => (int) $image->SortOrder,
                ]);
            }

            foreach ($sourceProduct->Attributes() as $attribute) {
                $newProduct->Attributes()->add($attribute);
            }

            foreach ($sourceProduct->AttributeOptions() as $option) {
                $newProduct->AttributeOptions()->add($option);
            }

            $duplicatedProduct = $newProduct;
        });

        if (!$duplicatedProduct) {
            throw new RuntimeException('The product could not be duplicated.');
        }

        return $duplicatedProduct;
    }

    private function getUniqueSKU(string $sourceSKU, int $targetAccountID): string
    {
        if (!$this->skuExists($sourceSKU, $targetAccountID)) {
            return $sourceSKU;
        }

        $copySKU = $sourceSKU . '-COPY';

        if (!$this->skuExists($copySKU, $targetAccountID)) {
            return $copySKU;
        }

        $suffix = 2;

        do {
            $copySKU = sprintf('%s-COPY-%d', $sourceSKU, $suffix++);
        } while ($this->skuExists($copySKU, $targetAccountID));

        return $copySKU;
    }

    private function skuExists(string $sku, int $targetAccountID): bool
    {
        return Product::get()
            ->filter([
                'SKU' => $sku,
                'CustomerAccountID' => $targetAccountID,
            ])
            ->exists();
    }
}
