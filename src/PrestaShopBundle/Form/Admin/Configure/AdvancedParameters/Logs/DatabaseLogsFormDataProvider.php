<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Form\Admin\Configure\AdvancedParameters\Logs;

use PrestaShop\PrestaShop\Adapter\Configuration\DatabaseLogsConfiguration;
use PrestaShop\PrestaShop\Core\Form\FormDataProviderInterface;

/**
 * This class is responsible of managing the data manipulated using database form
 * in "Configure > Advanced Parameters > Logs" page.
 */
final class DatabaseLogsFormDataProvider implements FormDataProviderInterface
{
    public function __construct(
        private readonly DatabaseLogsConfiguration $databaseLogsConfiguration
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public function getData()
    {
        return $this->databaseLogsConfiguration->getConfiguration();
    }

    /**
     * {@inheritdoc}
     */
    public function setData(array $data)
    {
        return $this->databaseLogsConfiguration->updateConfiguration($data);
    }
}
