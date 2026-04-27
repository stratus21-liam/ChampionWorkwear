<?php

namespace {

    use SilverStripe\CMS\Controllers\ContentController;
    use SilverStripe\Control\HTTPRequest;
    use SilverStripe\Control\HTTPResponse;
    use SilverStripe\ORM\ArrayList;
    use SilverStripe\ORM\FieldType\DBCurrency;
    use SilverStripe\Security\Security;
    use SilverStripe\View\ArrayData;
    use ShopModule\Model\Product;

    /**
     * @template T of Page
     * @extends ContentController<T>
     */
    class PageController extends ContentController
    {
        private const CART_SESSION_KEY = 'Shop.Cart';
        private const CART_MESSAGE_SESSION_KEY = 'Shop.CartMessage';
        private const VAT_RATE = 0.20;

        private static $allowed_actions = [
            'addToCart',
            'cart',
            'clearCart',
            'removeCartItem',
            'updateCartItem',
        ];

        // private static $url_handlers = [
        //     'add-to-cart' => 'addToCart',
        //     'cart' => 'cart',
        //     'clear-cart' => 'clearCart',
        //     'remove-cart-item/$Key!' => 'removeCartItem',
        //     'update-cart-item/$Key!' => 'updateCartItem',
        // ];

        protected function init()
        {
            parent::init();
        }

        protected function getCartSessionKey(): string
        {
            $member = Security::getCurrentUser();

            if ($member && $member->ID) {
                return self::CART_SESSION_KEY . '.Member.' . $member->ID;
            }

            return self::CART_SESSION_KEY . '.Guest';
        }

        protected function getCartMessageSessionKey(): string
        {
            $member = Security::getCurrentUser();

            if ($member && $member->ID) {
                return self::CART_MESSAGE_SESSION_KEY . '.Member.' . $member->ID;
            }

            return self::CART_MESSAGE_SESSION_KEY . '.Guest';
        }

        protected function setCartMessage(string $message): void
        {
            $this->getRequest()
                ->getSession()
                ->set($this->getCartMessageSessionKey(), $message);
        }

        public function addToCart(HTTPRequest $request): HTTPResponse
        {
            if (!$request->isPOST()) {
                return $this->httpError(405);
            }

            $productID = (int) $request->postVar('ProductID');
            $qty = (int) $request->postVar('Quantity');

            if ($qty < 1) {
                $qty = 1;
            }

            $product = Product::get()->byID($productID);

            if (!$product) {
                $this->setCartMessage('Invalid product.');
                return $this->redirectBack();
            }

            $attributes = $this->extractCartAttributes($product, $request->postVars());

            if ($product->Attributes()->exists()) {
                foreach ($product->Attributes() as $attribute) {
                    if (!$attribute->Required) {
                        continue;
                    }
                    if($attribute->Type != 'text_input'){
                        continue;
                    }
                    $attributeKey = 'attr_' . $attribute->ID;
                    $attributeData = $attributes[$attributeKey] ?? null;
                    $attributeValue = is_array($attributeData) ? ($attributeData['value'] ?? '') : '';

                    if (trim((string) $attributeValue) === '') {
                        $this->setCartMessage('Please select your options before adding this product to cart.');
                        return $this->redirect($product->Link());
                    }
                }
            }

            $this->addItemToCart([
                'product_id' => (int) $product->ID,
                'title' => (string) $product->Title,
                'sku' => (string) $product->SKU,
                'price' => (float) $product->Price,
                'qty' => $qty,
                'attributes' => $attributes,
            ]);

            $this->setCartMessage('Product added to cart.');

            return $this->redirectBack();
        }

        public function cart(HTTPRequest $request)
        {
            return $this->customise([
                'CartItems' => $this->CartItems(),
                'CartCount' => $this->CartCount(),
                'CartTotal' => $this->CartTotal(),
            ])->render();
        }

        public function clearCart(HTTPRequest $request): HTTPResponse
        {
            $this->getRequest()
                ->getSession()
                ->clear($this->getCartSessionKey());

            $this->setCartMessage('Cart cleared.');

            return $this->redirectBack();
        }

        public function removeCartItem(HTTPRequest $request): HTTPResponse
        {
            $key = (string) $request->param('ID');
            $cart = $this->getCartSession();

            if ($key && isset($cart[$key])) {
                unset($cart[$key]);
                $this->setCartSession($cart);
                $this->setCartMessage('Item removed from cart.');
            }

            return $this->redirectBack();
        }

        public function updateCartItem(HTTPRequest $request): HTTPResponse
        {
            $key = (string) $request->param('ID');
            $qty = (int) $request->postVar('Quantity');
            $cart = $this->getCartSession();

            if (!$key || !isset($cart[$key])) {
                return $this->redirectBack();
            }

            if ($qty <= 0) {
                unset($cart[$key]);
                $this->setCartMessage('Item removed from cart.');
            } else {
                $cart[$key]['qty'] = $qty;
                $cart[$key]['line_total'] = $cart[$key]['price'] * $qty;
                $this->setCartMessage('Cart updated.');
            }

            $this->setCartSession($cart);

            return $this->redirectBack();
        }

        protected function addItemToCart(array $data): string
        {
            $productID = (int) ($data['product_id'] ?? 0);
            $title = trim((string) ($data['title'] ?? ''));
            $sku = trim((string) ($data['sku'] ?? ''));
            $price = (float) ($data['price'] ?? 0);
            $qty = max(1, (int) ($data['qty'] ?? 1));
            $attributes = is_array($data['attributes'] ?? null) ? $data['attributes'] : [];

            ksort($attributes);

            $cart = $this->getCartSession();
            $itemKey = $this->buildCartItemKey($productID, $attributes);

            if (isset($cart[$itemKey])) {
                $cart[$itemKey]['qty'] += $qty;
                $cart[$itemKey]['line_total'] = $cart[$itemKey]['qty'] * $cart[$itemKey]['price'];
            } else {
                $cart[$itemKey] = [
                    'key' => $itemKey,
                    'product_id' => $productID,
                    'title' => $title,
                    'sku' => $sku,
                    'price' => $price,
                    'qty' => $qty,
                    'attributes' => $attributes,
                    'line_total' => $price * $qty,
                ];
            }

            $this->setCartSession($cart);

            return $itemKey;
        }

        protected function extractCartAttributes(Product $product, array $post): array
        {
            $attributes = [];
            $productAttributes = [];

            if ($product->Attributes()->exists()) {
                foreach ($product->Attributes() as $attribute) {
                    $productAttributes[(int) $attribute->ID] = $attribute;
                }
            }

            foreach ($post as $key => $value) {
                if (strpos($key, 'attr_') !== 0) {
                    continue;
                }

                $attributeID = (int) str_replace('attr_', '', $key);
                $attribute = $productAttributes[$attributeID] ?? null;

                $cleanValue = is_array($value)
                    ? array_values(array_filter(array_map('trim', $value), function ($item) {
                        return $item !== '';
                    }))
                    : trim((string) $value);

                if ($cleanValue === '' || $cleanValue === []) {
                    continue;
                }

                $attributes[$key] = [
                    'label' => $attribute ? (string) $attribute->Title : $key,
                    'value' => is_array($cleanValue) ? implode(', ', $cleanValue) : $cleanValue,
                ];
            }

            ksort($attributes);

            return $attributes;
        }

        protected function buildCartItemKey(int $productID, array $attributes): string
        {
            return md5($productID . ':' . json_encode($attributes));
        }

        protected function getCartSession(): array
        {
            $cart = $this->getRequest()
                ->getSession()
                ->get($this->getCartSessionKey());

            return is_array($cart) ? $cart : [];
        }

        protected function setCartSession(array $cart): void
        {
            $this->getRequest()
                ->getSession()
                ->set($this->getCartSessionKey(), $cart);
        }

        public function CartItems(): ArrayList
        {
            $items = ArrayList::create();

            foreach ($this->getCartSession() as $item) {
                $attributeList = ArrayList::create();
                $product = Product::get()->byID($item['product_id']);

                foreach (($item['attributes'] ?? []) as $key => $attribute) {
                    $label = '';
                    $value = '';

                    if (is_array($attribute)) {
                        $label = (string) ($attribute['label'] ?? $key);
                        $value = (string) ($attribute['value'] ?? '');
                    } else {
                        $label = $key;
                        $value = is_array($attribute) ? implode(', ', $attribute) : (string) $attribute;
                    }

                    $attributeList->push(ArrayData::create([
                        'Key' => $key,
                        'Label' => $label,
                        'Value' => $value,
                    ]));
                }

                $imageUrl = null;

                if ($product && $product->FeaturedImage()->exists()) {
                    $imageUrl = $product->FeaturedImage()->URL;
                }

                $lineTotal = (float) ($item['line_total'] ?? 0);
                $price = (float) ($item['price'] ?? 0);

                $items->push(ArrayData::create([
                    'Key' => $item['key'],
                    'ProductID' => $item['product_id'],
                    'Title' => $item['title'],
                    'SKU' => $item['sku'],
                    'Price' => DBCurrency::create_field('Currency', $price),
                    'Qty' => $item['qty'],
                    'LineTotal' => DBCurrency::create_field('Currency', $lineTotal),
                    'Attributes' => $attributeList,
                    'ImageURL' => $imageUrl,
                    'ProductLink' => $product ? $product->Link() : null,
                ]));
            }

            return $items;
        }

        public function CartCount(): int
        {
            $count = 0;

            foreach ($this->getCartSession() as $item) {
                $count += (int) ($item['qty'] ?? 0);
            }

            return $count;
        }

        public function CartTotal(): DBCurrency
        {
            $total = 0;

            foreach ($this->getCartSession() as $item) {
                $total += (float) ($item['line_total'] ?? 0);
            }

            return DBCurrency::create_field('Currency', $total);
        }

        public function CartTotalIncludingVAT(): DBCurrency
        {
            return DBCurrency::create_field(
                'Currency',
                (float) $this->CartTotal()->getValue() * (1 + self::VAT_RATE)
            );
        }

        public function CartMessage(): ?string
        {
            $session = $this->getRequest()->getSession();
            $messageKey = $this->getCartMessageSessionKey();
            $message = $session->get($messageKey);

            if ($message) {
                $session->clear($messageKey);
            }

            return $message;
        }
    }
}
