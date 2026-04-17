<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Meta;

use PrestaShop\PrestaShop\Core\Configuration\AbstractMultistoreConfiguration;
use PrestaShop\PrestaShop\Core\Exception\CoreException;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SEOOptionsDataConfiguration extends AbstractMultistoreConfiguration
{
    /**
     * @var array<int, string>
     */
    private const CONFIGURATION_FIELDS = [
        'product_attributes_in_title',
    ];

    /**
     * {@inheritdoc}
     */
    public function getConfiguration()
    {
        $shopConstraint = $this->getShopConstraint();

        return [
            'product_attributes_in_title' => (bool) $this->configuration->get('PS_PRODUCT_ATTRIBUTES_IN_TITLE', false, $shopConstraint),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function updateConfiguration(array $configuration)
    {
        $errors = [];
        try {
            if ($this->validateConfiguration($configuration)) {
                $shopConstraint = $this->getShopConstraint();

                $this->updateConfigurationValue('PS_PRODUCT_ATTRIBUTES_IN_TITLE', 'product_attributes_in_title', $configuration, $shopConstraint);
            }
        } catch (CoreException $exception) {
            $errors[] = $exception->getMessage();
        }

        return $errors;
    }

    /**
     * @return OptionsResolver
     */
    protected function buildResolver(): OptionsResolver
    {
        $resolver = (new OptionsResolver())
            ->setDefined(self::CONFIGURATION_FIELDS)
            ->setAllowedTypes('product_attributes_in_title', 'bool');

        return $resolver;
    }
}
