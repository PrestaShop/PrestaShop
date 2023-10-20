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
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace PrestaShop\PrestaShop\Core\Form\ChoiceProvider;

use PrestaShop\PrestaShop\Adapter\Customer\CustomerDataProvider;
use PrestaShop\PrestaShop\Core\Form\ConfigurableFormChoiceProviderInterface;

/**
 * Provides choices for configuring required fields for customer
 */
final class CustomerAddressesChoiceProvider implements ConfigurableFormChoiceProviderInterface
{
    /**
     * @var CustomerDataProvider
     */
    private $customerDataProvider;

    /**
     * @var int
     */
    private $langId;

    /**
     * @param CustomerDataProvider $customerDataProvider
     * @param int $langId
     */
    public function __construct(CustomerDataProvider $customerDataProvider, int $langId)
    {
        $this->customerDataProvider = $customerDataProvider;
        $this->langId = $langId;
    }

    /**
     * {@inheritdoc}
     */
    public function getChoices(array $options)
    {
        if (!isset($options['customer_id'])) {
            throw new \InvalidArgumentException('Expected a customer_id option, none found');
        }

        $addresses = $this->customerDataProvider->getCustomerAddresses($options['customer_id'], $this->langId);

        $result = [];
        foreach ($addresses as $address) {
            $description = sprintf(
                '%d- %s %s %s %s',
                $address['id_address'],
                $address['address1'],
                $address['address2'] ?: '',
                $address['postcode'],
                $address['city']
            );

            $result[$description] = $address['id_address'];
        }

        return $result;
    }
}
