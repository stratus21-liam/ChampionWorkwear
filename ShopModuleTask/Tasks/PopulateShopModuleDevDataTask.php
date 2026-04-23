<?php

namespace ShopModuleTask\Tasks;

use App\Model\Order;
use App\Model\OrderItem;
use ShopModule\Model\CustomerAccount;
use ShopModule\Model\Product;
use ShopModule\Model\ProductAttribute;
use ShopModule\Model\ProductAttributeOption;
use ShopModule\Model\Role;
use SilverStripe\Dev\BuildTask;
use SilverStripe\ORM\DB;
use SilverStripe\Security\Member;

class PopulateShopModuleDevDataTask extends BuildTask
{
    private const PASSWORD = 'qwerty123';

    private static $segment = 'populate-shopmodule-dev-data';

    protected $title = 'Populate ShopModule dev data';

    protected $description = 'Creates varied customer accounts, roles, users, products, attributes, and orders for ShopModule development.';

    public function run($request): void
    {
        $counts = [];

        DB::get_conn()->withTransaction(function () use (&$counts) {
            $clearTask = ClearShopModuleDevDataTask::create();
            $clearTask->clearData();

            $attributes = $this->createAttributes();
            $accounts = $this->createAccounts();
            $roles = $this->createRoles($accounts);
            $products = $this->createProducts($accounts, $roles, $attributes);
            $members = $this->createMembers($accounts, $roles);
            $orders = $this->createOrders($accounts, $members, $products);

            $counts = [
                'customer accounts' => count($accounts),
                'roles' => count($roles, COUNT_RECURSIVE) - count($roles),
                'attributes' => count($attributes),
                'products' => count($products, COUNT_RECURSIVE) - count($products),
                'members' => count($members, COUNT_RECURSIVE) - count($members),
                'orders' => $orders,
            ];
        });

        foreach ($counts as $label => $count) {
            echo sprintf('%s created: %d<br>', ucfirst($label), $count);
        }

        echo sprintf('All generated user passwords are: %s<br>', self::PASSWORD);
    }

    private function createAttributes(): array
    {
        $attributeData = [
            'colour' => [
                'Title' => 'Dev Seed Colour',
                'Code' => 'devseed_colour',
                'Type' => 'colour_radio',
                'Required' => true,
                'Options' => [
                    ['Title' => 'Red', 'HexColour' => '#d21f3c'],
                    ['Title' => 'Blue', 'HexColour' => '#1d5fbd'],
                    ['Title' => 'Green', 'HexColour' => '#1e8f4d'],
                ],
            ],
            'size' => [
                'Title' => 'Dev Seed Size',
                'Code' => 'devseed_size',
                'Type' => 'square_radio',
                'Required' => true,
                'Options' => [
                    ['Title' => 'Small', 'SquareLabel' => 'S'],
                    ['Title' => 'Medium', 'SquareLabel' => 'M'],
                    ['Title' => 'Large', 'SquareLabel' => 'L'],
                ],
            ],
            'personalisation' => [
                'Title' => 'Dev Seed Personalisation',
                'Code' => 'devseed_personalisation',
                'Type' => 'text_input',
                'Required' => false,
                'Placeholder' => 'Name or message',
                'MaxLength' => 40,
                'Options' => [],
            ],
            'finish' => [
                'Title' => 'Dev Seed Finish',
                'Code' => 'devseed_finish',
                'Type' => 'square_radio',
                'Required' => false,
                'Options' => [
                    ['Title' => 'Matte', 'SquareLabel' => 'MAT'],
                    ['Title' => 'Gloss', 'SquareLabel' => 'GLS'],
                ],
            ],
        ];

        $attributes = [];
        $sortOrder = 1;

        foreach ($attributeData as $key => $data) {
            $attribute = ProductAttribute::create();
            $attribute->Title = $data['Title'];
            $attribute->Code = $data['Code'];
            $attribute->Type = $data['Type'];
            $attribute->Required = $data['Required'];
            $attribute->Active = true;
            $attribute->Placeholder = $data['Placeholder'] ?? '';
            $attribute->MaxLength = $data['MaxLength'] ?? 0;
            $attribute->SortOrder = $sortOrder++;
            $attribute->write();

            $options = [];
            $optionSortOrder = 1;

            foreach ($data['Options'] as $optionData) {
                $option = ProductAttributeOption::create();
                $option->Title = $optionData['Title'];
                $option->Value = $optionData['Title'];
                $option->AttributeID = (int) $attribute->ID;
                $option->HexColour = $optionData['HexColour'] ?? '';
                $option->SquareLabel = $optionData['SquareLabel'] ?? '';
                $option->Active = true;
                $option->SortOrder = $optionSortOrder++;
                $option->write();

                $options[$option->Title] = $option;
            }

            $attributes[$key] = [
                'record' => $attribute,
                'options' => $options,
            ];
        }

        return $attributes;
    }

    private function createAccounts(): array
    {
        $accounts = [];

        foreach ([
            'apex' => ['Dev Seed - Apex Facilities', 'Apex Facilities Store'],
            'northstar' => ['Dev Seed - Northstar Retail', 'Northstar Retail Store'],
            'meridian' => ['Dev Seed - Meridian Events', 'Meridian Events Store'],
        ] as $key => [$title, $storeTitle]) {
            $account = CustomerAccount::create();
            $account->Title = $title;
            $account->StoreTitle = $storeTitle;
            $account->Active = true;
            $account->write();

            $accounts[$key] = $account;
        }

        return $accounts;
    }

    private function createRoles(array $accounts): array
    {
        $roleData = [
            'apex' => ['Facilities Buyer', 'Site Manager', 'Finance Approver'],
            'northstar' => ['Store Colleague', 'Visual Merchandiser', 'Regional Manager'],
            'meridian' => ['Event Crew', 'Production Lead', 'Client Services'],
        ];

        $roles = [];

        foreach ($roleData as $accountKey => $titles) {
            $sortOrder = 1;

            foreach ($titles as $title) {
                $role = Role::create();
                $role->Title = $title;
                $role->Description = sprintf('%s role for seeded development data.', $title);
                $role->CustomerAccountID = (int) $accounts[$accountKey]->ID;
                $role->SortOrder = $sortOrder++;
                $role->write();

                $roles[$accountKey][$title] = $role;
            }
        }

        return $roles;
    }

    private function createProducts(array $accounts, array $roles, array $attributes): array
    {
        $productData = [
            'apex' => [
                [
                    'Title' => 'Apex Hi-Vis Vest',
                    'SKU' => 'DEV-SEED-APX-VEST',
                    'Price' => 18.50,
                    'Roles' => ['Facilities Buyer', 'Site Manager'],
                    'Attributes' => ['size', 'colour'],
                    'Options' => ['size' => ['Small', 'Medium', 'Large'], 'colour' => ['Red', 'Blue']],
                ],
                [
                    'Title' => 'Apex Safety Helmet',
                    'SKU' => 'DEV-SEED-APX-HELMET',
                    'Price' => 31.25,
                    'Roles' => ['Site Manager', 'Finance Approver'],
                    'Attributes' => ['colour', 'personalisation'],
                    'Options' => ['colour' => ['Blue', 'Green']],
                ],
            ],
            'northstar' => [
                [
                    'Title' => 'Northstar Staff Polo',
                    'SKU' => 'DEV-SEED-NOR-POLO',
                    'Price' => 22.00,
                    'Roles' => ['Store Colleague', 'Regional Manager'],
                    'Attributes' => ['size', 'colour', 'personalisation'],
                    'Options' => ['size' => ['Small', 'Medium', 'Large'], 'colour' => ['Red', 'Green']],
                ],
                [
                    'Title' => 'Northstar Display Pack',
                    'SKU' => 'DEV-SEED-NOR-DISPLAY',
                    'Price' => 48.75,
                    'Roles' => ['Visual Merchandiser', 'Regional Manager'],
                    'Attributes' => ['finish'],
                    'Options' => ['finish' => ['Matte', 'Gloss']],
                ],
            ],
            'meridian' => [
                [
                    'Title' => 'Meridian Crew Lanyard',
                    'SKU' => 'DEV-SEED-MER-LANYARD',
                    'Price' => 6.95,
                    'Roles' => ['Event Crew', 'Production Lead'],
                    'Attributes' => ['colour'],
                    'Options' => ['colour' => ['Red', 'Blue', 'Green']],
                ],
                [
                    'Title' => 'Meridian Event Jacket',
                    'SKU' => 'DEV-SEED-MER-JACKET',
                    'Price' => 64.00,
                    'Roles' => ['Production Lead', 'Client Services'],
                    'Attributes' => ['size', 'finish'],
                    'Options' => ['size' => ['Medium', 'Large'], 'finish' => ['Matte']],
                ],
            ],
        ];

        $products = [];

        foreach ($productData as $accountKey => $items) {
            foreach ($items as $data) {
                $product = Product::create();
                $product->Title = $data['Title'];
                $product->SKU = $data['SKU'];
                $product->Price = $data['Price'];
                $product->Description = sprintf('<p>Seeded product for %s.</p>', $accounts[$accountKey]->Title);
                $product->Active = true;
                $product->CustomerAccountID = (int) $accounts[$accountKey]->ID;
                $product->write();

                foreach ($data['Roles'] as $roleTitle) {
                    $product->Roles()->add($roles[$accountKey][$roleTitle]);
                }

                foreach ($data['Attributes'] as $attributeKey) {
                    $product->Attributes()->add($attributes[$attributeKey]['record']);
                }

                foreach ($data['Options'] as $attributeKey => $optionTitles) {
                    foreach ($optionTitles as $optionTitle) {
                        $product->AttributeOptions()->add($attributes[$attributeKey]['options'][$optionTitle]);
                    }
                }

                $product->write();
                $products[$accountKey][] = $product;
            }
        }

        return $products;
    }

    private function createMembers(array $accounts, array $roles): array
    {
        $memberData = [
            'apex' => [
                ['Avery', 'Admin', 'apex.admin', true, null, false, 0, false],
                ['Blair', 'Buyer', 'apex.buyer', false, 'Facilities Buyer', true, 75, true],
                ['Casey', 'Manager', 'apex.manager', false, 'Site Manager', false, 0, false],
            ],
            'northstar' => [
                ['Devon', 'Admin', 'northstar.admin', true, null, true, 250, false],
                ['Emery', 'Colleague', 'northstar.colleague', false, 'Store Colleague', true, 35, true],
                ['Finley', 'Merchandiser', 'northstar.merch', false, 'Visual Merchandiser', false, 0, true],
            ],
            'meridian' => [
                ['Gray', 'Admin', 'meridian.admin', true, null, false, 0, false],
                ['Harper', 'Crew', 'meridian.crew', false, 'Event Crew', true, 20, true],
                ['Indigo', 'Producer', 'meridian.producer', false, 'Production Lead', true, 150, false],
            ],
        ];

        $members = [];

        foreach ($memberData as $accountKey => $items) {
            foreach ($items as [$firstName, $surname, $emailPrefix, $isAdmin, $roleTitle, $enableSpendLimit, $spendLimit, $requiresApproval]) {
                $member = Member::create();
                $member->FirstName = $firstName;
                $member->Surname = $surname;
                $member->Email = $emailPrefix . '@devseed.champion.local';
                $member->CustomerAccountID = (int) $accounts[$accountKey]->ID;
                $member->RoleID = $roleTitle ? (int) $roles[$accountKey][$roleTitle]->ID : 0;
                $member->IsAdmin = $isAdmin;
                $member->Active = true;
                $member->EnableSpendLimit = $enableSpendLimit;
                $member->SpendLimit = $enableSpendLimit ? $spendLimit : 0;
                $member->RequiresApproval = $requiresApproval;
                $member->write();
                $member->changePassword(self::PASSWORD);

                $members[$accountKey][] = $member;
            }
        }

        return $members;
    }

    private function createOrders(array $accounts, array $members, array $products): int
    {
        $count = 0;

        foreach ($members as $accountKey => $accountMembers) {
            foreach ($accountMembers as $memberIndex => $member) {
                $accountProducts = $products[$accountKey];

                for ($i = 0; $i < 2; $i++) {
                    $product = $accountProducts[($memberIndex + $i) % count($accountProducts)];
                    $status = $this->statusFor($member, $i);
                    $requiresApproval = $status === Order::STATUS_PENDING_APPROVAL || (bool) $member->RequiresApproval;
                    $orderIndex = $memberIndex + $i + 1;
                    $fulfilmentMethod = $i === 0 ? 'delivery' : 'collection';

                    $order = Order::create();
                    $order->OrderNumber = sprintf('DEV-SEED-%s-%02d-%02d', strtoupper($accountKey), $memberIndex + 1, $i + 1);
                    $order->Status = $status;
                    $order->RequiresApproval = $requiresApproval;
                    $order->CustomerID = (int) $member->ID;
                    $order->CustomerAccountID = (int) $accounts[$accountKey]->ID;
                    $order->FulfilmentMethod = $fulfilmentMethod;
                    $order->PONumber = sprintf('PO-DEV-%s-%02d-%02d', strtoupper($accountKey), $memberIndex + 1, $i + 1);
                    $order->OrderNotes = sprintf('Seeded order for %s.', $member->Email);
                    $order->SubmittedAt = date('Y-m-d H:i:s', strtotime(sprintf('-%d days', $orderIndex)));

                    if ($status === Order::STATUS_APPROVED) {
                        $order->ApprovedAt = date('Y-m-d H:i:s', strtotime(sprintf('-%d days', max(0, $orderIndex - 1))));
                    }

                    if ($status === Order::STATUS_REJECTED) {
                        $order->RejectedAt = date('Y-m-d H:i:s', strtotime(sprintf('-%d days', max(0, $orderIndex - 1))));
                        $order->RejectionReason = 'Seeded rejection reason for approval testing.';
                    }

                    if ($fulfilmentMethod === 'delivery') {
                        $order->DeliveryCompany = $accounts[$accountKey]->Title;
                        $order->DeliveryContactName = $member->FirstName . ' ' . $member->Surname;
                        $order->DeliveryPhone = '020 7946 0000';
                        $order->DeliveryEmail = $member->Email;
                        $order->DeliveryAddressLine1 = sprintf('%d Dev Seed Street', 10 + $memberIndex);
                        $order->DeliveryAddressLine2 = 'Test Trading Estate';
                        $order->DeliveryCity = 'London';
                        $order->DeliveryCounty = 'Greater London';
                        $order->DeliveryPostcode = 'SW1A 1AA';
                    }

                    $order->write();

                    $this->createOrderItem($order, $product, $memberIndex + $i + 1);

                    $order->Subtotal = $order->getItemsSubtotal();
                    $order->Total = $order->Subtotal;
                    $order->write();
                    $count++;
                }
            }
        }

        return $count;
    }

    private function createOrderItem(Order $order, Product $product, int $quantity): void
    {
        $options = [];

        foreach ($product->Attributes() as $attribute) {
            $key = 'attr_' . $attribute->ID;

            if ($attribute->Type === 'text_input') {
                $options[$key] = [
                    'label' => $attribute->Title,
                    'value' => 'Dev Seed ' . $order->Customer()->FirstName,
                ];
                continue;
            }

            $option = $product->AttributeOptions()
                ->filter('AttributeID', $attribute->ID)
                ->first();

            if ($option) {
                $options[$key] = [
                    'label' => $attribute->Title,
                    'value' => $option->Title,
                ];
            }
        }

        $item = OrderItem::create();
        $item->OrderID = (int) $order->ID;
        $item->ProductID = (int) $product->ID;
        $item->ProductTitle = $product->Title;
        $item->SKU = $product->SKU;
        $item->Quantity = max(1, $quantity);
        $item->UnitPrice = (float) $product->Price;
        $item->OptionsJSON = json_encode($options);
        $item->write();
    }

    private function statusFor(Member $member, int $orderOffset): string
    {
        if ($orderOffset === 0 && (bool) $member->RequiresApproval) {
            return Order::STATUS_PENDING_APPROVAL;
        }

        if ($orderOffset === 1 && (bool) $member->IsAdmin) {
            return Order::STATUS_REJECTED;
        }

        return Order::STATUS_APPROVED;
    }
}
