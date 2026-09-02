<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Form\Admin\Configure\ShopParameters\CustomerPreferences;

use PrestaShop\PrestaShop\Core\Configuration\DataConfigurationInterface;
use PrestaShop\PrestaShop\Core\Form\FormDataProviderInterface;

/**
 * Class is responsible of managing the data manipulated using forms
 * in "Configure > Shop Parameters > Customer Settings" page.
 */
final class CustomerPreferencesDataProvider implements FormDataProviderInterface
{
    /**
     * @var DataConfigurationInterface
     */
    private $generalDataConfiguration;

    public function __construct(DataConfigurationInterface $generalDataConfiguration)
    {
        $this->generalDataConfiguration = $generalDataConfiguration;
    }

    /**
     * {@inheritdoc}
     */
    public function getData()
    {
        return $this->generalDataConfiguration->getConfiguration();
    }

    /**
     * {@inheritdoc}
     */
    public function setData(array $data)
    {
        return $this->generalDataConfiguration->updateConfiguration($data);
    }
}
