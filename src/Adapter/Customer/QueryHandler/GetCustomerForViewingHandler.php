<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace PrestaShop\PrestaShop\Adapter\Customer\QueryHandler;

use Carrier;
use Cart;
use CartRule;
use Category;
use Context;
use Currency;
use Customer;
use CustomerThread;
use Db;
use Gender;
use Group;
use Language;
use Link;
use Order;
use PrestaShop\PrestaShop\Adapter\LegacyContext;
use PrestaShop\PrestaShop\Core\Domain\Customer\Exception\CustomerNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Customer\Query\GetCustomerForViewing;
use PrestaShop\PrestaShop\Core\Domain\Customer\QueryHandler\GetCustomerForViewingHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Customer\QueryResult\AddressInformation;
use PrestaShop\PrestaShop\Core\Domain\Customer\QueryResult\BoughtProductInformation;
use PrestaShop\PrestaShop\Core\Domain\Customer\QueryResult\CartInformation;
use PrestaShop\PrestaShop\Core\Domain\Customer\QueryResult\DiscountInformation;
use PrestaShop\PrestaShop\Core\Domain\Customer\QueryResult\GeneralInformation;
use PrestaShop\PrestaShop\Core\Domain\Customer\QueryResult\GroupInformation;
use PrestaShop\PrestaShop\Core\Domain\Customer\QueryResult\LastConnectionInformation;
use PrestaShop\PrestaShop\Core\Domain\Customer\QueryResult\MessageInformation;
use PrestaShop\PrestaShop\Core\Domain\Customer\QueryResult\OrderInformation;
use PrestaShop\PrestaShop\Core\Domain\Customer\QueryResult\OrdersInformation;
use PrestaShop\PrestaShop\Core\Domain\Customer\QueryResult\PersonalInformation;
use PrestaShop\PrestaShop\Core\Domain\Customer\QueryResult\ProductsInformation;
use PrestaShop\PrestaShop\Core\Domain\Customer\QueryResult\ReferrerInformation;
use PrestaShop\PrestaShop\Core\Domain\Customer\QueryResult\SentEmailInformation;
use PrestaShop\PrestaShop\Core\Domain\Customer\QueryResult\Subscriptions;
use PrestaShop\PrestaShop\Core\Domain\Customer\QueryResult\ViewableCustomer;
use PrestaShop\PrestaShop\Core\Domain\Customer\QueryResult\ViewedProductInformation;
use PrestaShop\PrestaShop\Core\Domain\Customer\ValueObject\CustomerId;
use PrestaShop\PrestaShop\Core\Localization\Locale;
use Product;
use Referrer;
use Shop;
use Symfony\Component\Translation\TranslatorInterface;
use Tools;
use Validate;

/**
 * Handles commands which gets customer for viewing in Back Office.
 *
 * @internal
 */
final class GetCustomerForViewingHandler implements GetCustomerForViewingHandlerInterface
{
    /**
     * @var LegacyContext
     */
    private $context;

    /**
     * @var int
     */
    private $contextLangId;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var Link
     */
    private $link;

    /**
     * @var Locale
     */
    private $locale;

    /**
     * @param TranslatorInterface $translator
     * @param int $contextLangId
     * @param Link $link
     * @param Locale $locale
     */
    public function __construct(
        TranslatorInterface $translator,
        $contextLangId,
        Link $link,
        Locale $locale
    ) {
        $this->context = new LegacyContext();
        $this->contextLangId = $contextLangId;
        $this->translator = $translator;
        $this->link = $link;
        $this->locale = $locale;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(GetCustomerForViewing $query)
    {
        $customerId = $query->getCustomerId();
        $customer = new Customer($customerId->getValue());

        $this->assertCustomerWasFound($customerId, $customer);

        Context::getContext()->customer = $customer;

        return new ViewableCustomer(
            $customerId,
            $this->getGeneralInformation($customer),
            $this->getPersonalInformation($customer),
            $this->getCustomerOrders($customer),
            $this->getCustomerCarts($customer),
            $this->getCustomerProducts($customer),
            $this->getCustomerMessages($customer),
            $this->getCustomerDiscounts($customer),
            $this->getLastEmailsSentToCustomer($customer),
            $this->getLastCustomerConnections($customer),
            $this->getCustomerGroups($customer),
            $this->getCustomerReferrers($customer),
            $this->getCustomerAddresses($customer)
        );
    }

    /**
     * @param Customer $customer
     *
     * @return GeneralInformation
     */
    private function getGeneralInformation(Customer $customer)
    {
        return new GeneralInformation(
            $customer->note,
            Customer::customerExists($customer->email)
        );
    }

    /**
     * @param Customer $customer
     *
     * @return PersonalInformation
     */
    private function getPersonalInformation(Customer $customer)
    {
        $customerStats = $customer->getStats();

        $gender = new Gender($customer->id_gender, $this->contextLangId);
        $socialTitle = $gender->name ?: $this->translator->trans('Unknown', [], 'Admin.Orderscustomers.Feature');

        if ($customer->birthday && '0000-00-00' !== $customer->birthday) {
            $birthday = sprintf(
                $this->translator->trans('%1$d years old (birth date: %2$s)', [], 'Admin.Orderscustomers.Feature'),
                $customerStats['age'],
                Tools::displayDate($customer->birthday)
            );
        } else {
            $birthday = $this->translator->trans('Unknown', [], 'Admin.Orderscustomers.Feature');
        }

        $registrationDate = Tools::displayDate($customer->date_add, null, true);
        $lastUpdateDate = Tools::displayDate($customer->date_upd, null, true);
        $lastVisitDate = $customerStats['last_visit'] ?
            Tools::displayDate($customerStats['last_visit'], null, true) :
            $this->translator->trans('Never', [], 'Admin.Global');

        $customerShop = new Shop($customer->id_shop);
        $customerLanguage = new Language($customer->id_lang);

        $customerSubscriptions = new Subscriptions(
            (bool) $customer->newsletter,
            (bool) $customer->optin
        );

        return new PersonalInformation(
            $customer->firstname,
            $customer->lastname,
            $customer->email,
            $customer->isGuest(),
            $socialTitle,
            $birthday,
            $registrationDate,
            $lastUpdateDate,
            $lastVisitDate,
            $this->getCustomerRankBySales($customer->id),
            $customerShop->name,
            $customerLanguage->name,
            $customerSubscriptions,
            (bool) $customer->active
        );
    }

    /**
     * @param int $customerId
     *
     * @return int|null customer rank or null if customer is not ranked
     */
    private function getCustomerRankBySales($customerId)
    {
        $sql = 'SELECT SUM(total_paid_real) FROM ' . _DB_PREFIX_ . 'orders WHERE id_customer = ' . (int) $customerId . ' AND valid = 1';

        if ($totalPaid = Db::getInstance()->getValue($sql)) {
            $sql = '
                SELECT SQL_CALC_FOUND_ROWS COUNT(*)
                FROM ' . _DB_PREFIX_ . 'orders
                WHERE valid = 1
                    AND id_customer != ' . (int) $customerId . '
                GROUP BY id_customer
                HAVING SUM(total_paid_real) > ' . (int) $totalPaid;

            Db::getInstance()->getValue($sql);

            return (int) Db::getInstance()->getValue('SELECT FOUND_ROWS()') + 1;
        }

        return null;
    }

    /**
     * @param Customer $customer
     *
     * @return OrdersInformation
     */
    private function getCustomerOrders(Customer $customer)
    {
        $validOrders = [];
        $invalidOrders = [];

        $orders = Order::getCustomerOrders($customer->id, true);
        $totalSpent = 0;

        foreach ($orders as $order) {
            $order['total_paid_real_not_formated'] = $order['total_paid_real'];
            $order['total_paid_real'] = $this->locale->formatPrice(
                $order['total_paid_real'],
                Currency::getIsoCodeById((int) $order['id_currency'])
            );

            if (!isset($order['order_state'])) {
                $order['order_state'] = $this->translator->trans(
                    'There is no status defined for this order.',
                    [],
                    'Admin.Orderscustomers.Notification'
                );
            }

            $customerOrderInformation = new OrderInformation(
                (int) $order['id_order'],
                Tools::displayDate($order['date_add']),
                $order['payment'],
                $order['order_state'],
                (int) $order['nb_products'],
                $order['total_paid_real']
            );

            if ($order['valid']) {
                $validOrders[] = $customerOrderInformation;
                $totalSpent += $order['total_paid_real_not_formated'] / $order['conversion_rate'];
            } else {
                $invalidOrders[] = $customerOrderInformation;
            }
        }

        return new OrdersInformation(
            $this->locale->formatPrice($totalSpent, $this->context->getContext()->currency->iso_code),
            $validOrders,
            $invalidOrders
        );
    }

    /**
     * @param Customer $customer
     *
     * @return CartInformation[]
     */
    private function getCustomerCarts(Customer $customer)
    {
        $carts = Cart::getCustomerCarts($customer->id);
        $customerCarts = [];

        foreach ($carts as $cart) {
            $cart = new Cart((int) $cart['id_cart']);
            Context::getContext()->cart = $cart;

            $currency = new Currency($cart->id_currency);
            Context::getContext()->currency = $currency;

            $carrier = new Carrier($cart->id_carrier);
            $summary = $cart->getSummaryDetails();

            $customerCarts[] = new CartInformation(
                sprintf('%06d', $cart->id),
                Tools::displayDate($cart->date_add, null, true),
                $this->locale->formatPrice($summary['total_price'], $currency->iso_code),
                $carrier->name
            );
        }

        Context::getContext()->currency = Currency::getDefaultCurrency();

        return $customerCarts;
    }

    /**
     * @param Customer $customer
     *
     * @return ProductsInformation
     */
    private function getCustomerProducts(Customer $customer)
    {
        $boughtProducts = [];
        $viewedProducts = [];

        $products = $customer->getBoughtProducts();
        foreach ($products as $product) {
            $boughtProducts[] = new BoughtProductInformation(
                (int) $product['id_order'],
                Tools::displayDate($product['date_add'], null, false),
                $product['product_name'],
                $product['product_quantity']
            );
        }

        $sql = '
            SELECT DISTINCT cp.id_product, c.id_cart, c.id_shop, cp.id_shop AS cp_id_shop
            FROM ' . _DB_PREFIX_ . 'cart_product cp
            JOIN ' . _DB_PREFIX_ . 'cart c ON (c.id_cart = cp.id_cart)
            JOIN ' . _DB_PREFIX_ . 'product p ON (cp.id_product = p.id_product)
            WHERE c.id_customer = ' . (int) $customer->id . '
                AND NOT EXISTS (
                        SELECT 1
                        FROM ' . _DB_PREFIX_ . 'orders o
                        JOIN ' . _DB_PREFIX_ . 'order_detail od ON (o.id_order = od.id_order)
                        WHERE product_id = cp.id_product AND o.valid = 1 AND o.id_customer = ' . (int) $customer->id . '
                )
        ';

        $viewedProductsData = Db::getInstance()->executeS($sql);
        foreach ($viewedProductsData as $productData) {
            $product = new Product(
                $productData['id_product'],
                false,
                $this->contextLangId,
                $productData['id_shop']
            );

            if (!Validate::isLoadedObject($product)) {
                continue;
            }

            $productUrl = $this->link->getProductLink(
                $product->id,
                $product->link_rewrite,
                Category::getLinkRewrite($product->id_category_default, $this->contextLangId),
                null,
                null,
                $productData['cp_id_shop']
            );

            $viewedProducts[] = new ViewedProductInformation(
                (int) $product->id,
                $product->name,
                $productUrl
            );
        }

        return new ProductsInformation(
            $boughtProducts,
            $viewedProducts
        );
    }

    /**
     * @param Customer $customer
     *
     * @return MessageInformation[]
     */
    private function getCustomerMessages(Customer $customer)
    {
        $customerMessages = [];
        $messages = CustomerThread::getCustomerMessages((int) $customer->id);

        $messageStatuses = [
            'open' => $this->translator->trans('Open', [], 'Admin.Orderscustomers.Feature'),
            'closed' => $this->translator->trans('Closed', [], 'Admin.Orderscustomers.Feature'),
            'pending1' => $this->translator->trans('Pending 1', [], 'Admin.Orderscustomers.Feature'),
            'pending2' => $this->translator->trans('Pending 2', [], 'Admin.Orderscustomers.Feature'),
        ];

        foreach ($messages as $message) {
            $status = isset($messageStatuses[$message['status']]) ?
                $messageStatuses[$message['status']] :
                $message['status'];

            $customerMessages[] = new MessageInformation(
                (int) $message['id_customer_thread'],
                substr(strip_tags(html_entity_decode($message['message'], ENT_NOQUOTES, 'UTF-8')), 0, 75),
                $status,
                Tools::displayDate($message['date_add'], null, true)
            );
        }

        return $customerMessages;
    }

    /**
     * @param Customer $customer
     *
     * @return DiscountInformation[]
     */
    private function getCustomerDiscounts(Customer $customer)
    {
        $discounts = CartRule::getCustomerCartRules($this->contextLangId, $customer->id, false, false);
        $customerDiscounts = [];

        foreach ($discounts as $discount) {
            $availableQuantity = $discount['quantity'] > 0 ? (int) $discount['quantity_for_user'] : 0;

            $customerDiscounts[] = new DiscountInformation(
                (int) $discount['id_cart_rule'],
                $discount['code'],
                $discount['name'],
                (bool) $discount['active'],
                $availableQuantity
            );
        }

        return $customerDiscounts;
    }

    /**
     * @param Customer $customer
     *
     * @return SentEmailInformation[]
     */
    private function getLastEmailsSentToCustomer(Customer $customer)
    {
        $emails = $customer->getLastEmails();
        $customerEmails = [];

        foreach ($emails as $email) {
            $customerEmails[] = new SentEmailInformation(
                Tools::displayDate($email['date_add'], null, true),
                $email['language'],
                $email['subject'],
                $email['template']
            );
        }

        return $customerEmails;
    }

    /**
     * @param Customer $customer
     *
     * @return LastConnectionInformation[]
     */
    private function getLastCustomerConnections(Customer $customer)
    {
        $connections = $customer->getLastConnections();
        $lastConnections = [];

        if (!is_array($connections)) {
            $connections = [];
        }

        foreach ($connections as $connection) {
            $httpReferer = $connection['http_referer'] ?
                preg_replace('/^www./', '', parse_url($connection['http_referer'], PHP_URL_HOST)) :
                $this->translator->trans('Direct link', [], 'Admin.Orderscustomers.Notification');

            $lastConnections[] = new LastConnectionInformation(
                $connection['id_connections'],
                Tools::displayDate($connection['date_add']),
                $connection['pages'],
                $connection['time'],
                $httpReferer,
                $connection['ipaddress']
            );
        }

        return $lastConnections;
    }

    /**
     * @param Customer $customer
     *
     * @return GroupInformation[]
     */
    private function getCustomerGroups(Customer $customer)
    {
        $groups = $customer->getGroups();
        $customerGroups = [];

        foreach ($groups as $groupId) {
            $group = new Group($groupId);

            $customerGroups[] = new GroupInformation(
                (int) $group->id,
                $group->name[$this->contextLangId]
            );
        }

        return $customerGroups;
    }

    /**
     * @param Customer $customer
     *
     * @return ReferrerInformation[]
     */
    private function getCustomerReferrers(Customer $customer)
    {
        $referrers = Referrer::getReferrers($customer->id);
        $customerReferrers = [];

        foreach ($referrers as $referrer) {
            $customerReferrers[] = new ReferrerInformation(
                Tools::displayDate($referrer['date_add'], null, true),
                $referrer['name'],
                $referrer['shop_name']
            );
        }

        return $customerReferrers;
    }

    /**
     * @param Customer $customer
     *
     * @return AddressInformation[]
     */
    private function getCustomerAddresses(Customer $customer)
    {
        $addresses = $customer->getAddresses($this->contextLangId);
        $customerAddresses = [];

        foreach ($addresses as $address) {
            $company = $address['company'] ?: '--';
            $fullAddress = sprintf(
                '%s %s %s %s',
                $address['address1'],
                $address['address2'] ?: '',
                $address['postcode'],
                $address['city']
            );

            $customerAddresses[] = new AddressInformation(
                (int) $address['id_address'],
                $company,
                sprintf('%s %s', $address['firstname'], $address['lastname']),
                $fullAddress,
                $address['country'],
                (string) $address['phone'],
                (string) $address['phone_mobile']
            );
        }

        return $customerAddresses;
    }

    /**
     * @param CustomerId $customerId
     * @param Customer $customer
     *
     * @throws CustomerNotFoundException
     */
    private function assertCustomerWasFound(CustomerId $customerId, Customer $customer)
    {
        if (!$customer->id) {
            throw new CustomerNotFoundException($customerId, sprintf('Customer with id "%s" was not found.', $customerId->getValue()));
        }
    }
}
