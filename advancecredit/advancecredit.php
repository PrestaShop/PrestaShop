<?php

if (!defined('_PS_VERSION_')) {
    exit;
}

use PrestaShop\PrestaShop\Core\Payment\PaymentOption;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\DataColumn;

class Advancecredit extends PaymentModule
{
    private $creditRepository = null;

    public function __construct()
    {
        $this->name = 'advancecredit';
        $this->tab = 'payments_gateways';
        $this->version = '1.0.0';
        $this->author = 'Twój Autor';
        $this->need_instance = 0;
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Kredyt kupiecki');
        $this->description = $this->l('Zarządzanie kredytem kupieckim dla klientów B2B.');
        $this->ps_versions_compliancy = ['min' => '8.0.0', 'max' => '9.99.99'];
    }

    private function getCreditRepository()
    {
        if ($this->creditRepository === null) {
            $repoPath = __DIR__ . '/src/Domain/CustomerCreditRepository.php';
            if (!file_exists($repoPath)) {
                throw new \Exception('Brak pliku repozytorium: ' . $repoPath);
            }
            require_once $repoPath;
            $this->creditRepository = new CustomerCreditRepository();
        }

        return $this->creditRepository;
    }

    public function install()
    {
        return parent::install()
            && $this->installDb()
            && $this->registerHook('actionObjectCustomerAddAfter')
            && $this->registerHook('actionCustomerFormBuilderModifier')
            && $this->registerHook('actionCustomerFormDataProviderData')
            && $this->registerHook('actionAfterCreateCustomerFormHandler')
            && $this->registerHook('actionAfterUpdateCustomerFormHandler')
            && $this->registerHook('displayAdminCustomersExtra')
            && $this->registerHook('paymentOptions')
            && $this->registerHook('displayCustomerAccount')
            && $this->registerHook('actionCustomerGridDefinitionModifier')
            && $this->registerHook('actionCustomerGridQueryBuilderModifier')
            && $this->registerHook('actionOrderStatusPostUpdate')
            && $this->registerHook('displayAdminCustomers');
    }

    public function uninstall()
    {
        return parent::uninstall() && $this->uninstallDb();
    }

    private function installDb()
    {
        $sql = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'customer_credit` (
            `id_customer_credit` INT(11) NOT NULL AUTO_INCREMENT,
            `id_customer` INT(11) NOT NULL,
            `credit_limit` DECIMAL(20,6) NOT NULL DEFAULT 0.000000,
            `current_debt` DECIMAL(20,6) NOT NULL DEFAULT 0.000000,
            `date_add` DATETIME NOT NULL,
            `date_upd` DATETIME NOT NULL,
            PRIMARY KEY  (`id_customer_credit`),
            UNIQUE KEY (`id_customer`)
        ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;';

        return Db::getInstance()->execute($sql);
    }

    private function uninstallDb()
    {
        return Db::getInstance()->execute('DROP TABLE IF EXISTS `' . _DB_PREFIX_ . 'customer_credit`');
    }

    public function hookActionObjectCustomerAddAfter(array $params)
    {
        $customer = $params['object'];
        $this->getCreditRepository()->updateCreditLimit((int)$customer->id, 0.0);
    }

    /**
     * Modyfikacja formularza Symfony klienta w PS 9.3
     */
    public function hookActionCustomerFormBuilderModifier(array $params)
    {
        $fieldPath = __DIR__ . '/src/Form/CustomerCreditField.php';
        if (file_exists($fieldPath)) {
            require_once $fieldPath;
            CustomerCreditField::addCreditLimitField(
                $params['form_builder'],
                $this->l('Kredyt kupiecki (PLN)')
            );
        }
    }


    public function hookActionCustomerFormDataProviderData(array $params): void
    {
        $customerId = (int)($params['id'] ?? $params['id_customer'] ?? 0);
        if ($customerId > 0) {
            $creditData = $this->getCreditRepository()->getCreditData($customerId);
            $params['data']['credit_limit'] = $creditData['credit_limit'];
        }
    }

    public function hookActionAfterCreateCustomerFormHandler(array $params)
    {
        $this->saveCreditLimitFromForm($params);
    }

    public function hookActionAfterUpdateCustomerFormHandler(array $params)
    {
        $this->saveCreditLimitFromForm($params);
    }

    private function saveCreditLimitFromForm(array $params): void
    {
        $customerId = (int)($params['id'] ?? $params['id_customer'] ?? 0);
        if (!$customerId && isset($params['customer'])) {
            $customerId = (int)$params['customer']->id;
        }

        if ($customerId > 0 && isset($params['form_data']['credit_limit'])) {
            $rawLimit = $params['form_data']['credit_limit'];
            $cleanLimit = (float)str_replace(',', '.', (string)$rawLimit);
            $this->getCreditRepository()->updateCreditLimit($customerId, max(0, $cleanLimit));
        }
    }

    public function hookDisplayAdminCustomersExtra(array $params): string
    {
        return '<div class="alert alert-info">TEST ADVANCE CREDIT - HOOK DZIAŁA</div>';
    }

    public function hookDisplayAdminCustomers($params): string
    {
        return $this->hookDisplayAdminCustomersExtra($params);
    }

    public function hookDisplayAdminCustomersExtra($params): string
    {
        $idCustomer = (int)(
            $params['id_customer']
            ?? $params['idCustomer']
            ?? $params['id']
            ?? $params['customerId']
            ?? 0
        );

        if (!$idCustomer && !empty($params['customer'])) {
            $customer = $params['customer'];
            $idCustomer = (int)(is_object($customer) ? ($customer->id ?? 0) : ($customer['id'] ?? 0));
        }

        if (!$idCustomer) {
            return '';
        }

        $creditRepo = $this->getCreditRepository();
        $creditData = $creditRepo->getByCustomerId($idCustomer);

        $limit = $creditData ? (float)$creditData['credit_limit'] : 0.00;
        $debt = $creditData ? (float)$creditData['current_debt'] : 0.00;

        $this->context->smarty->assign([
            'credit_limit' => $limit,
            'current_debt' => $debt,
            'available_credit' => max(0, $limit - $debt),
        ]);

        return $this->display(__FILE__, 'views/templates/admin/customer_credit_block.tpl');
    }

    /**
     * Kolumna "Dostępny Kredyt" w tabeli klientów BO
     */
    public function hookActionCustomerGridDefinitionModifier(array $params)
    {
        $definition = $params['definition'];
        if ($definition->getId() !== 'customer') {
            return;
        }

        $definition->getColumns()->addAfter(
            'optin',
            (new DataColumn('available_credit'))
                ->setName($this->l('Dostępny Kredyt'))
                ->setOptions([
                    'field' => 'available_credit',
                ])
        );
    }

    public function hookActionCustomerGridQueryBuilderModifier(array $params)
    {
        $searchQueryBuilder = $params['search_query_builder'];

        $searchQueryBuilder->addSelect(
            'IFNULL(cc.credit_limit - cc.current_debt, 0) AS available_credit'
        );
        $searchQueryBuilder->leftJoin(
            'c',
            _DB_PREFIX_ . 'customer_credit',
            'cc',
            'c.id_customer = cc.id_customer'
        );
    }

    /**
     * Płatność w checkoutcie
     */
    public function hookPaymentOptions(array $params)
    {
        if (!$this->active) {
            return [];
        }

        $cart = $params['cart'];
        $customerId = (int) $cart->id_customer;
        if (!$customerId) {
            return [];
        }

        $creditData = $this->getCreditRepository()->getCreditData($customerId);
        $availableCredit = $this->getCreditRepository()->getAvailableCredit($customerId);
        $cartTotal = (float) $cart->getOrderTotal(true, Cart::BOTH);

        if ($availableCredit < $cartTotal) {
            return [];
        }

        $this->context->smarty->assign([
            'credit_limit' => $creditData['credit_limit'],
            'current_debt' => $creditData['current_debt'],
            'available_credit' => $availableCredit,
        ]);

        $additionalInfo = $this->context->smarty->fetch('module:advancecredit/views/templates/hook/payment_info.tpl');

        $newOption = new PaymentOption();
        $newOption->setCallToActionText($this->l('Kredyt kupiecki (Dostępny: ' . number_format($availableCredit, 2, ',', ' ') . ' PLN)'))
            ->setAction($this->context->link->getModuleLink($this->name, 'validation', [], true))
            ->setAdditionalInformation($additionalInfo);

        return [$newOption];
    }

    public function hookDisplayCustomerAccount(array $params)
    {
        return $this->context->smarty->fetch('module:advancecredit/views/templates/hook/customer_account.tpl');
    }

    /**
     * Automatyczny zwrot limitu przy zmianie statusu zamówienia na "Anulowane" lub "Zwrócone"
     */
    public function hookActionOrderStatusPostUpdate(array $params): void
    {
        $idOrder = (int)($params['id_order'] ?? 0);
        $newOrderStatus = $params['newOrderStatus'] ?? null;

        if (!$idOrder || !$newOrderStatus) {
            return;
        }

        $canceledStatusId = (int)Configuration::get('PS_OS_CANCELED');
        $refundedStatusId = (int)Configuration::get('PS_OS_REFUND');

        // Sprawdzamy, czy nowy status to "Anulowane" lub "Zwrócone"
        if ((int)$newOrderStatus->id !== $canceledStatusId && (int)$newOrderStatus->id !== $refundedStatusId) {
            return;
        }

        $order = new Order($idOrder);
        if (!Validate::isLoadedObject($order)) {
            return;
        }

        // Reagujemy tylko na zamówienia opłacone tym modułem
        if ($order->module !== $this->name) {
            return;
        }

        $customerId = (int)$order->id_customer;
        $orderTotal = (float)$order->total_paid_tax_incl;

        // Zwrot kwoty zamówienia do dostępnego limitu klienta
        $this->getCreditRepository()->decreaseDebt($customerId, $orderTotal);
    }
}
