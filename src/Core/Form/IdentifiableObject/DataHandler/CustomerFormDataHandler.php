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

namespace PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataHandler;

use DateTime;
use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Domain\Customer\Command\AddCustomerCommand;
use PrestaShop\PrestaShop\Core\Domain\Customer\Command\EditCustomerCommand;
use PrestaShop\PrestaShop\Core\Domain\Customer\ValueObject\Birthday;
use PrestaShop\PrestaShop\Core\Domain\Customer\ValueObject\CustomerId;
use PrestaShop\PrestaShop\Core\Domain\Customer\ValueObject\Email;
use PrestaShop\PrestaShop\Core\Domain\Customer\ValueObject\FirstName;
use PrestaShop\PrestaShop\Core\Domain\Customer\ValueObject\LastName;
use PrestaShop\PrestaShop\Core\Domain\Customer\ValueObject\Password;

/**
 * Saves or updates customer data submitted in form
 */
final class CustomerFormDataHandler implements FormDataHandlerInterface
{
    /**
     * @var CommandBusInterface
     */
    private $bus;

    /**
     * @var int
     */
    private $contextShopId;

    /**
     * @var bool
     */
    private $isB2bFeatureEnabled;

    /**
     * @param CommandBusInterface $bus
     * @param int $contextShopId
     * @param bool $isB2bFeatureEnabled
     */
    public function __construct(
        CommandBusInterface $bus,
        $contextShopId,
        $isB2bFeatureEnabled
    ) {
        $this->bus = $bus;
        $this->contextShopId = $contextShopId;
        $this->isB2bFeatureEnabled = $isB2bFeatureEnabled;
    }

    /**
     * {@inheritdoc}
     */
    public function create(array $data)
    {
        $command = $this->buildCustomerAddCommandFromFormData($data);

        /** @var CustomerId $customerId */
        $customerId = $this->bus->handle($command);

        return $customerId->getValue();
    }

    /**
     * {@inheritdoc}
     */
    public function update($customerId, array $data)
    {
        $command = $this->buildCustomerEditCommand($customerId, $data);

        /** @var CustomerId $customerId */
        $customerId = $this->bus->handle($command);

        return $customerId->getValue();
    }

    /**
     * @param array $data
     *
     * @return AddCustomerCommand
     */
    private function buildCustomerAddCommandFromFormData(array $data)
    {
        $command = new AddCustomerCommand(
            new FirstName($data['first_name']),
            new LastName($data['last_name']),
            new Email($data['email']),
            new Password($data['password']),
            (int) $data['default_group_id'],
            array_map(function ($groupId) { return (int) $groupId; }, $data['group_ids']),
            $this->contextShopId,
            (int) $data['gender_id'],
            (bool) $data['is_enabled'],
            (bool) $data['is_partner_offers_subscribed'],
            new Birthday($this->getBirthdayDate($data['birthday']))
        );

        if (!$this->isB2bFeatureEnabled) {
            return $command;
        }

        $command
            ->setCompanyName($data['company_name'])
            ->setSiretCode($data['siret_code'])
            ->setApeCode($data['ape_code'])
            ->setWebsite($data['website'])
            ->setAllowedOutstandingAmount($data['allowed_outstanding_amount'])
            ->setMaxPaymentDays($data['max_payment_days'])
            ->setRiskId($data['risk_id'])
        ;

        return $command;
    }

    /**
     * @param int $customerId
     * @param array $data
     *
     * @return EditCustomerCommand
     */
    private function buildCustomerEditCommand($customerId, array $data)
    {
        $command = (new EditCustomerCommand(new CustomerId($customerId)))
            ->setGenderId($data['gender_id'])
            ->setEmail(new Email($data['email']))
            ->setFirstName(new FirstName($data['first_name']))
            ->setLastName(new LastName($data['last_name']))
            ->setIsEnabled($data['is_enabled'])
            ->setIsPartnerOffersSubscribed($data['is_partner_offers_subscribed'])
            ->setDefaultGroupId((int) $data['default_group_id'])
            ->setGroupIds(array_map(function ($groupId) { return (int) $groupId; }, $data['group_ids']))
            ->setBirthday(new Birthday($this->getBirthdayDate($data['birthday'])))
        ;

        if ($this->isB2bFeatureEnabled) {
            $command
                ->setCompanyName((string) $data['company_name'])
                ->setSiretCode((string) $data['siret_code'])
                ->setApeCode((string) $data['ape_code'])
                ->setWebsite((string) $data['website'])
                ->setAllowedOutstandingAmount((float) $data['allowed_outstanding_amount'])
                ->setMaxPaymentDays((int) $data['max_payment_days'])
                ->setRiskId((int) $data['risk_id'])
            ;
        }

        return $command;
    }

    /**
     * Since it's used in context of form data
     * the value provided by form is either DateTime (when date is selected) or null (when not selected)
     * in case birthday is null, it means that user wants to reset birthday
     * or keep it default (not set)
     *
     * @param DateTime|null $formBirthdayValue
     *
     * @return string
     */
    private function getBirthdayDate($formBirthdayValue)
    {
        return $formBirthdayValue instanceof DateTime ?
            $formBirthdayValue->format('Y-m-d') :
            Birthday::EMPTY_BIRTHDAY
        ;
    }
}
