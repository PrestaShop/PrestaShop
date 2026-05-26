<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\CustomerService\Configuration;

use PrestaShop\PrestaShop\Core\Configuration\AbstractMultistoreConfiguration;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Persists the "Contact options" panel of the Customer Service settings:
 * the customer-side file upload toggle and the translatable default
 * employee signature used when replying to a thread.
 */
final class CustomerServiceOptionsConfiguration extends AbstractMultistoreConfiguration
{
    private const CONFIGURATION_FIELDS = [
        'file_upload',
        'signature',
    ];

    /**
     * {@inheritdoc}
     */
    public function getConfiguration()
    {
        $shopConstraint = $this->getShopConstraint();

        return [
            'file_upload' => (bool) $this->configuration->get('PS_CUSTOMER_SERVICE_FILE_UPLOAD', false, $shopConstraint),
            'signature' => $this->configuration->get('PS_CUSTOMER_SERVICE_SIGNATURE', [], $shopConstraint),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function updateConfiguration(array $configuration)
    {
        if ($this->validateConfiguration($configuration)) {
            $shopConstraint = $this->getShopConstraint();

            $this->updateConfigurationValue('PS_CUSTOMER_SERVICE_FILE_UPLOAD', 'file_upload', $configuration, $shopConstraint);
            $this->updateConfigurationValue('PS_CUSTOMER_SERVICE_SIGNATURE', 'signature', $configuration, $shopConstraint);
        }

        return [];
    }

    protected function buildResolver(): OptionsResolver
    {
        return (new OptionsResolver())
            ->setDefined(self::CONFIGURATION_FIELDS)
            ->setAllowedTypes('file_upload', 'bool')
            ->setAllowedTypes('signature', 'array');
    }
}
