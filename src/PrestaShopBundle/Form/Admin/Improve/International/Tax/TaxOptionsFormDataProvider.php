<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Form\Admin\Improve\International\Tax;

use PrestaShop\PrestaShop\Core\Configuration\DataConfigurationInterface;
use PrestaShop\PrestaShop\Core\Form\FormDataProviderInterface;

/**
 * Provides data for tax options form
 */
final class TaxOptionsFormDataProvider implements FormDataProviderInterface
{
    /**
     * @var DataConfigurationInterface
     */
    private $taxOptionsDataConfiguration;

    /**
     * @param DataConfigurationInterface $taxOptionsDataConfiguration
     */
    public function __construct(DataConfigurationInterface $taxOptionsDataConfiguration)
    {
        $this->taxOptionsDataConfiguration = $taxOptionsDataConfiguration;
    }

    /**
     * {@inheritdoc}
     */
    public function getData()
    {
        return $this->taxOptionsDataConfiguration->getConfiguration();
    }

    /**
     * {@inheritdoc}
     */
    public function setData(array $data)
    {
        return $this->taxOptionsDataConfiguration->updateConfiguration($data);
    }
}
