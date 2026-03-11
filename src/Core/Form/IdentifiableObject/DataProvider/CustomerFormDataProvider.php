<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataProvider;

use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\ConfigurationInterface;
use PrestaShop\PrestaShop\Core\Domain\Customer\Query\GetCustomerForEditing;
use PrestaShop\PrestaShop\Core\Domain\Customer\QueryResult\EditableCustomer;
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
        $editableCustomer = $this->queryBus->handle(new GetCustomerForEditing((int) $customerId));
        $birthday = $editableCustomer->getBirthday();

        $data = [
            'gender_id' => $editableCustomer->getGenderId(),
            'first_name' => $editableCustomer->getFirstName()->getValue(),
            'last_name' => $editableCustomer->getLastName()->getValue(),
            'email' => $editableCustomer->getEmail()->getValue(),
            'birthday' => $birthday->isEmpty() ? null : $birthday->getValue(),
            'is_enabled' => $editableCustomer->isEnabled(),
            'is_newsletter_subscribed' => $editableCustomer->isNewsletterSubscribed(),
            'is_partner_offers_subscribed' => $editableCustomer->isPartnerOffersSubscribed(),
            'group_ids' => $editableCustomer->getGroupIds(),
            'default_group_id' => $editableCustomer->getDefaultGroupId(),
            'is_guest' => $editableCustomer->isGuest(),
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
            'is_guest' => false,
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
