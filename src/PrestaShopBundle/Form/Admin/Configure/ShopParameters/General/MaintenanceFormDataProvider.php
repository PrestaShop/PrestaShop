<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Form\Admin\Configure\ShopParameters\General;

use PrestaShop\PrestaShop\Adapter\Shop\MaintenanceConfiguration;
use PrestaShop\PrestaShop\Core\Form\FormDataProviderInterface;

/**
 * This class is responsible of managing the data manipulated using forms
 * in "Configure > Shop Parameters > General > Maintenance" page.
 */
final class MaintenanceFormDataProvider implements FormDataProviderInterface
{
    /**
     * @var MaintenanceConfiguration
     */
    private $maintenanceConfiguration;

    public function __construct(MaintenanceConfiguration $maintenanceConfiguration)
    {
        $this->maintenanceConfiguration = $maintenanceConfiguration;
    }

    /**
     * {@inheritdoc}
     */
    public function getData()
    {
        return $this->maintenanceConfiguration->getConfiguration();
    }

    /**
     * {@inheritdoc}
     */
    public function setData(array $data)
    {
        return $this->maintenanceConfiguration->updateConfiguration($data);
    }
}
