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

namespace PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataProvider;

use DateTime;
use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\ConfigurationInterface;
use PrestaShop\PrestaShop\Core\Domain\Customer\Dto\EditableCustomer;
use PrestaShop\PrestaShop\Core\Domain\Customer\Query\GetCustomerForEditing;
use PrestaShop\PrestaShop\Core\Domain\Customer\ValueObject\CustomerId;
use PrestaShop\PrestaShop\Core\Group\Provider\DefaultGroupsProviderInterface;

/**
 * Provides data for customer forms
 */
final class CustomerFormDataProvider implements FormDataProviderInterface
{
    /**
     * @var CommandBusInterface
     */
    private $queryBus;

    /**
     * @var ConfigurationInterface
     */
    private $configuration;

    /**
     * @var DefaultGroupsProviderInterface
     */
    private $defaultGroupsProvider;

    /**
     * @var bool
     */
    private $isB2bFeatureEnabled;

    /**
     * @param CommandBusInterface $queryBus
     * @param ConfigurationInterface $configuration
     * @param DefaultGroupsProviderInterface $defaultGroupsProvider
     * @param bool $isB2bFeatureEnabled
     */
    public function __construct(
        CommandBusInterface $queryBus,
        ConfigurationInterface $configuration,
        DefaultGroupsProviderInterface $defaultGroupsProvider,
        $isB2bFeatureEnabled
    ) {
        $this->queryBus = $queryBus;
        $this->configuration = $configuration;
        $this->defaultGroupsProvider = $defaultGroupsProvider;
        $this->isB2bFeatureEnabled = $isB2bFeatureEnabled;
    }

    /**
     * {@inheritdoc}
     */
    public function getData($customerId)
    {
        /** @var EditableCustomer $editableCustomer */
        $editableCustomer = $this->queryBus->handle(new GetCustomerForEditing(new CustomerId((int) $customerId)));

        $birthday = '0000-00-00' !== $editableCustomer->getBirthday() ?
            new DateTime($editableCustomer->getBirthday()) :
            null
        ;

        $data = [
            'gender_id' => $editableCustomer->getGenderId(),
            'first_name' => $editableCustomer->getFirstName(),
            'last_name' => $editableCustomer->getLastName(),
            'email' => $editableCustomer->getEmail(),
            'birthday' => $birthday,
            'is_enabled' => $editableCustomer->isEnabled(),
            'is_partner_offers_subscribed' => $editableCustomer->isPartnerOffersSubscribed(),
            'group_ids' => $editableCustomer->getGroupIds(),
            'default_group_id' => $editableCustomer->getDefaultGroupId(),
        ];

        if ($this->isB2bFeatureEnabled) {
            $data = array_merge($data, [
                'company_name' => $editableCustomer->getCompanyName(),
                'siret_code' => $editableCustomer->getSiretCode(),
                'ape_code' => $editableCustomer->getApeCode(),
                'website' => $editableCustomer->getWebsite(),
                'allowed_outstanding_amount' => $editableCustomer->getAllowedOutstandingAmount(),
                'max_payment_days' => $editableCustomer->getMaxPaymentDays(),
                'risk_id' => $editableCustomer->getRiskId(),
            ]);
        }

        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultData()
    {
        $defaultGroups = $this->defaultGroupsProvider->getGroups();

        $data = [
            'is_enabled' => true,
            'is_partner_offers_subscribed' => false,
            'group_ids' => [
                $defaultGroups->getVisitorsGroup()->getId(),
                $defaultGroups->getGuestsGroup()->getId(),
                $defaultGroups->getCustomersGroup()->getId(),
            ],
            'default_group_id' => (int) $this->configuration->get('PS_CUSTOMER_GROUP'),
        ];

        if ($this->isB2bFeatureEnabled) {
            $data = array_merge($data, [
                'allowed_outstanding_amount' => 0,
                'max_payment_days' => 0,
            ]);
        }

        return $data;
    }
}
