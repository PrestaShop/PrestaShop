<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Form\Admin\Configure\ShopParameters\General;

use PrestaShop\PrestaShop\Adapter\Preferences\PreferencesConfiguration;
use PrestaShop\PrestaShop\Core\Form\FormDataProviderInterface;

/**
 * This class is responsible of managing the data manipulated using forms
 * in "Configure > Shop Parameters > General" page.
 */
final class PreferencesFormDataProvider implements FormDataProviderInterface
{
    /**
     * @var PreferencesConfiguration
     */
    private $preferencesConfiguration;

    public function __construct(PreferencesConfiguration $preferencesConfiguration)
    {
        $this->preferencesConfiguration = $preferencesConfiguration;
    }

    /**
     * {@inheritdoc}
     */
    public function getData()
    {
        return $this->preferencesConfiguration->getConfiguration();
    }

    /**
     * {@inheritdoc}
     */
    public function setData(array $data)
    {
        return $this->preferencesConfiguration->updateConfiguration($data);
    }
}
