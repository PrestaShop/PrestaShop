<?php
/**
 * 2007-2018 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
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
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Adapter\Customer\QueryHandler;

use Customer;
use Db;
use Gender;
use Language;
use PrestaShop\PrestaShop\Core\Domain\Customer\Dto\CustomerInformation\CustomerInformation;
use PrestaShop\PrestaShop\Core\Domain\Customer\Dto\CustomerInformation\PersonalInformation;
use PrestaShop\PrestaShop\Core\Domain\Customer\Dto\CustomerInformation\Subscriptions;
use PrestaShop\PrestaShop\Core\Domain\Customer\Exception\CustomerNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Customer\Query\GetCustomerInformation;
use PrestaShop\PrestaShop\Core\Domain\Customer\QueryHandler\GetCustomerInformationHandlerInterface;
use Shop;
use Symfony\Component\Translation\TranslatorInterface;
use Tools;

/**
 * Class GetCustomerInformationHandler
 */
final class GetCustomerInformationHandler implements GetCustomerInformationHandlerInterface
{
    /**
     * @var int
     */
    private $contextLangId;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @param TranslatorInterface $translator
     * @param $contextLangId
     */
    public function __construct(
        TranslatorInterface $translator,
        $contextLangId
    ) {
        $this->contextLangId = $contextLangId;
        $this->translator = $translator;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(GetCustomerInformation $query)
    {
        $customerId = $query->getCustomerId();
        $customer = new Customer($customerId->getValue());

        if (!$customer->id) {
            throw new CustomerNotFoundException(
                $customerId,
                sprintf('Customer with id "%s" was not found.', $customerId->getValue())
            );
        }

        return new CustomerInformation(
            $customerId,
            $this->getGeneralInformation($customer)
        );
    }

    /**
     * @param Customer $customer
     *
     * @return PersonalInformation
     */
    private function getGeneralInformation(Customer $customer)
    {
        $customerStats = $customer->getStats();

        $gender = new Gender($customer->id_gender, $this->contextLangId);
        $socialTitle = $gender->name ?: $this->translator->trans('Unknown', [], 'Admin.Orderscustomers.Feature');

        if ($customer->birthday && '0000-00-00' !== $customer->birthday) {
            $birthday = $this->translator->trans(
                '%1$d years old (birth date: %2$s)',
                [
                    Tools::displayDate($customer->birthday),
                    $customerStats
                ],
                'Admin.Orderscustomers.Feature'
            );
        } else {
            $birthday = $this->translator->trans('Unknown', [], 'Admin.Orderscustomers.Feature');
        }

        $registrationDate = Tools::displayDate($customer->date_add, null, true);
        $lastUpdateDate = Tools::displayDate($customer->date_upd, null, true);
        $lastVisitDate = $customerStats['last_visit'] ?
            Tools::displayDate($customerStats['last_visit'], null, true) :
            $this->translator->trans('Never', [], 'Admin.Global')
        ;

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
            $customerSubscriptions
        );
    }

    /**
     * @param int $customerId
     *
     * @return int|null Customer rank or null if customer is not ranked.
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
                HAVING SUM(total_paid_real) > ' . (int) $totalPaid
            ;

            Db::getInstance()->getValue($sql);

            return (int) Db::getInstance()->getValue('SELECT FOUND_ROWS()') + 1;
        }

        return null;
    }
}
