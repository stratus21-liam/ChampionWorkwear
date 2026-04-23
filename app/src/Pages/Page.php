<?php

namespace {

    use SilverStripe\CMS\Model\SiteTree;
    use SilverStripe\Control\Controller;

    class Page extends SiteTree
    {
        private static $db = [];

        private static $has_one = [];

        public function getYear()
        {
            return date('Y');
        }

        /**
         * returns the current controller class
         */
        public function getControllerClass()
        {
            $class = Controller::curr();
            return $class->ClassName;
        }

        protected function getSinglePageByClass(string $className)
        {
            if (!class_exists($className)) {
                return null;
            }

            return $className::get()->first();
        }

        /* ========================
           CART
        ======================== */

        public function getCartPage()
        {
            return $this->getSinglePageByClass(\App\Pages\CartPage::class);
        }

        public function getCartPageLink()
        {
            $page = $this->getCartPage();
            return $page ? $page->Link() : null;
        }

        /* ========================
           CHECKOUT
        ======================== */

        public function getCheckoutPage()
        {
            return $this->getSinglePageByClass(\App\Pages\CheckoutPage::class);
        }

        public function getCheckoutPageLink()
        {
            $page = $this->getCheckoutPage();
            return $page ? $page->Link() : null;
        }

        /* ========================
           ACCOUNT
        ======================== */

        public function getAccountPage()
        {
            return $this->getSinglePageByClass(\App\Pages\AccountPage::class);
        }

        public function getAccountPageLink()
        {
            $page = $this->getAccountPage();
            return $page ? $page->Link() : null;
        }

        /* ========================
           CREATE USERS
        ======================== */

        public function getManageUsersPage()
        {
            return $this->getSinglePageByClass(\App\Pages\ManageUsersPage::class);
        }

        public function getManageUsersPageLink()
        {
            $page = $this->getManageUsersPage();
            return $page ? $page->Link() : null;
        }

        /* ========================
           ORDER HISTORY
        ======================== */

        public function getOrderHistoryPage()
        {
            return $this->getSinglePageByClass(\App\Pages\OrderHistoryPage::class);
        }

        public function getOrderHistoryPageLink()
        {
            $page = $this->getOrderHistoryPage();
            return $page ? $page->Link() : null;
        }

        /* ========================
           PENDING ORDERS
        ======================== */

        public function getPendingOrdersPage()
        {
            return $this->getSinglePageByClass(\App\Pages\PendingOrdersPage::class);
        }

        public function getPendingOrdersPageLink()
        {
            $page = $this->getPendingOrdersPage();
            return $page ? $page->Link() : null;
        }

        /* ========================
           DASHBOARD
        ======================== */

        public function getDashboardPage()
        {
            return $this->getSinglePageByClass(\App\Pages\DashboardPage::class);
        }

        public function getDashboardPageLink()
        {
            $page = $this->getDashboardPage();
            return $page ? $page->Link() : null;
        }        
    }
}