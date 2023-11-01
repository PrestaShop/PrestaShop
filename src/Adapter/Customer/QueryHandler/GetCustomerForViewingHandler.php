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

use Context;
use Customer;
use CustomerThread;
use Db;
use Gender;
use Group;
use Language;
use PrestaShop\PrestaShop\Adapter\LegacyContext;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsQueryHandler;
use PrestaShop\PrestaShop\Core\Domain\Customer\Exception\CustomerNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Customer\Query\GetCustomerForViewing;
use PrestaShop\PrestaShop\Core\Domain\Customer\QueryHandler\GetCustomerForViewingHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Customer\QueryResult\GeneralInformation;
use PrestaShop\PrestaShop\Core\Domain\Customer\QueryResult\GroupInformation;
use PrestaShop\PrestaShop\Core\Domain\Customer\QueryResult\LastConnectionInformation;
use PrestaShop\PrestaShop\Core\Domain\Customer\QueryResult\MessageInformation;
use PrestaShop\PrestaShop\Core\Domain\Customer\QueryResult\OrdersInformation;
use PrestaShop\PrestaShop\Core\Domain\Customer\QueryResult\PersonalInformation;
use PrestaShop\PrestaShop\Core\Domain\Customer\QueryResult\ProductsInformation;
use PrestaShop\PrestaShop\Core\Domain\Customer\QueryResult\SentEmailInformation;
use PrestaShop\PrestaShop\Core\Domain\Customer\QueryResult\Subscriptions;
use PrestaShop\PrestaShop\Core\Domain\Customer\QueryResult\ViewableCustomer;
use PrestaShop\PrestaShop\Core\Domain\Customer\ValueObject\CustomerId;
use PrestaShop\PrestaShop\Core\Localization\Locale;
use Shop;
use Symfony\Contracts\Translation\TranslatorInterface;
use Tools;

/**
 * Handles commands which gets customer for viewing in Back Office.
 *
 * @internal
 */
#[AsQueryHandler]
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
     * @var Locale
     */
    private $locale;

    /**
     * @param TranslatorInterface $translator
     * @param int $contextLangId
     * @param Locale $locale
     */
    public function __construct(
        TranslatorInterface $translator,
        $contextLangId,
        Locale $locale
    ) {
        $this->context = new LegacyContext();
        $this->contextLangId = $contextLangId;
        $this->translator = $translator;
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
            [],
            $this->getCustomerProducts($customer),
            $this->getCustomerMessages($customer),
            [],
            $this->getLastEmailsSentToCustomer($customer),
            $this->getLastCustomerConnections($customer),
            $this->getCustomerGroups($customer),
            []
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

        $registrationDate = Tools::displayDate($customer->date_add, true);
        $lastUpdateDate = Tools::displayDate($customer->date_upd, true);
        $lastVisitDate = $customerStats['last_visit'] ?
            Tools::displayDate($customerStats['last_visit'], true) :
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
    private function getCustomerRankBySales($customerId): ?int
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
    private function getCustomerOrders(Customer $customer): OrdersInformation
    {
        $validOrders = [];
        $invalidOrders = [];
        $ordersTotal = 0;

        // Get fast order information
        $sql = '
        SELECT o.id_order, o.valid, o.total_paid_tax_incl, o.conversion_rate FROM `' . _DB_PREFIX_ . 'orders` o
        WHERE o.`id_customer` = ' . (int) $customer->id .
        Shop::addSqlRestriction(Shop::SHARE_ORDER) . '
        GROUP BY o.`id_order`';
        $orders = Db::getInstance()->executeS($sql);

        foreach ($orders as $order) {
            if ($order['valid']) {
                $validOrders[] = $order;
                $ordersTotal += $order['total_paid_tax_incl'] / $order['conversion_rate'];
            } else {
                $invalidOrders[] = $order;
            }
        }

        return new OrdersInformation(
            $this->locale->formatPrice($ordersTotal, $this->context->getContext()->currency->iso_code),
            $validOrders,
            $invalidOrders
        );
    }

    /**
     * @param Customer $customer
     *
     * @return ProductsInformation
     */
    private function getCustomerProducts(Customer $customer)
    {
        return new ProductsInformation([], []);
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
                Tools::displayDate($message['date_add'], true)
            );
        }

        return $customerMessages;
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
                Tools::displayDate($email['date_add'], true),
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
                $group->name[$this->contextLangId],
                (int) $group->id === (int) $customer->id_default_group
            );
        }

        return $customerGroups;
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
            throw new CustomerNotFoundException(sprintf('Customer with id "%d" was not found.', $customerId->getValue()));
        }
    }
}
