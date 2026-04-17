<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Form\Admin\Sell\CustomerService\MerchandiseReturn;

use PrestaShop\PrestaShop\Core\Configuration\DataConfigurationInterface;
use PrestaShop\PrestaShop\Core\Form\FormDataProviderInterface;

/**
 * Provides merchandise return options form with data
 */
final class MerchandiseReturnOptionsFormDataProvider implements FormDataProviderInterface
{
    /**
     * @var DataConfigurationInterface
     */
    private $optionsDataConfiguration;

    /**
     * @param DataConfigurationInterface $optionsDataConfiguration
     */
    public function __construct(DataConfigurationInterface $optionsDataConfiguration)
    {
        $this->optionsDataConfiguration = $optionsDataConfiguration;
    }

    /**
     * {@inheritdoc}
     */
    public function getData()
    {
        return $this->optionsDataConfiguration->getConfiguration();
    }

    /**
     * {@inheritdoc}
     */
    public function setData(array $data)
    {
        return $this->optionsDataConfiguration->updateConfiguration($data);
    }
}
