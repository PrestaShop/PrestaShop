<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Form\ChoiceProvider;

use InvalidArgumentException;
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
            throw new InvalidArgumentException('Expected a customer_id option, none found');
        }

        $addresses = $this->customerDataProvider->getCustomerAddresses($options['customer_id'], $this->langId);

        $result = [];
        foreach ($addresses as $address) {
            $description = sprintf(
                '#%d %s - %s %s %s %s',
                $address['id_address'],
                $address['alias'],
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
