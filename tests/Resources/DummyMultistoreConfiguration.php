<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Resources;

use PrestaShop\PrestaShop\Core\Configuration\AbstractMultistoreConfiguration;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DummyMultistoreConfiguration extends AbstractMultistoreConfiguration
{
    /**
     * @return array
     */
    public function getConfiguration(): array
    {
        $shopConstraint = $this->getShopConstraint();

        return [
            'test_conf_1' => (bool) $this->configuration->get('TEST_CONF_1', null, $shopConstraint),
            'test_conf_2' => $this->configuration->get('TEST_CONF_2', null, $shopConstraint),
        ];
    }

    /**
     * @param array $configurationInputValues
     *
     * @return array
     */
    public function updateConfiguration(array $configurationInputValues): array
    {
        if ($this->validateConfiguration($configurationInputValues)) {
            $shopConstraint = $this->getShopConstraint();

            $this->updateConfigurationValue('TEST_CONF_1', 'test_conf_1', $configurationInputValues, $shopConstraint);
            $this->updateConfigurationValue('TEST_CONF_2', 'test_conf_2', $configurationInputValues, $shopConstraint);
        }

        return [];
    }

    /**
     * @return OptionsResolver
     */
    public function buildResolver(): OptionsResolver
    {
        $resolver = new OptionsResolver();
        $resolver->setDefined(['test_conf_1', 'test_conf_2']);
        $resolver->setAllowedTypes('test_conf_1', 'bool');
        $resolver->setAllowedTypes('test_conf_2', 'string');

        return $resolver;
    }

    // wrapper public method to test the protected "updateConfigurationValue" method in unit tests
    public function dummyUpdateConfigurationValue($fieldName, $inputValues, $shopConstraint)
    {
        $this->updateConfigurationValue('PS_CONF_KEY', $fieldName, $inputValues, $shopConstraint);
    }
}
