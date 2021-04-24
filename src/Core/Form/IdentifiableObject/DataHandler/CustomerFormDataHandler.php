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

namespace PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataHandler;

use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Domain\Customer\Command\AddCustomerCommand;
use PrestaShop\PrestaShop\Core\Domain\Customer\Command\EditCustomerCommand;
use PrestaShop\PrestaShop\Core\Domain\Customer\ValueObject\Birthday;
use PrestaShop\PrestaShop\Core\Domain\Customer\ValueObject\CustomerId;

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

        $this->bus->handle($command);
    }

    /**
     * @param array $data
     *
     * @return AddCustomerCommand
     */
    private function buildCustomerAddCommandFromFormData(array $data)
    {
        $groupIds = array_map(function ($groupId) {
            return (int) $groupId;
        }, $data['group_ids']);

        $command = new AddCustomerCommand(
            $data['first_name'],
            $data['last_name'],
            $data['email'],
            $data['password'],
            (int) $data['default_group_id'],
            $groupIds,
            $this->contextShopId,
            (int) $data['gender_id'],
            (bool) $data['is_enabled'],
            (bool) $data['is_partner_offers_subscribed'],
            $data['birthday'] ?: Birthday::EMPTY_BIRTHDAY
        );

        if (!$this->isB2bFeatureEnabled) {
            return $command;
        }

        $command
            ->setCompanyName((string) $data['company_name'])
            ->setSiretCode((string) $data['siret_code'])
            ->setApeCode((string) $data['ape_code'])
            ->setWebsite((string) $data['website'])
            ->setAllowedOutstandingAmount((float) $data['allowed_outstanding_amount'])
            ->setMaxPaymentDays((int) $data['max_payment_days'])
            ->setRiskId((int) $data['risk_id'])
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
        $groupIds = array_map(function ($groupId) {
            return (int) $groupId;
        }, $data['group_ids']);

        $command = (new EditCustomerCommand($customerId))
            ->setGenderId($data['gender_id'])
            ->setEmail($data['email'])
            ->setFirstName($data['first_name'])
            ->setLastName($data['last_name'])
            ->setIsEnabled($data['is_enabled'])
            ->setIsPartnerOffersSubscribed($data['is_partner_offers_subscribed'])
            ->setDefaultGroupId((int) $data['default_group_id'])
            ->setGroupIds($groupIds)
            ->setBirthday($data['birthday'] ?: Birthday::EMPTY_BIRTHDAY)
        ;

        if (null !== $data['password']) {
            $command->setPassword($data['password']);
        }

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
}
