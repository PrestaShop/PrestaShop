<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Form\Admin\Configure\AdvancedParameters\Logs;

use PrestaShop\PrestaShop\Adapter\Configuration\LogsConfiguration;
use PrestaShop\PrestaShop\Core\Form\FormDataProviderInterface;

/**
 * This class is responsible of managing the data manipulated using forms
 * in "Configure > Advanced Parameters > Performance" page.
 */
final class LogsFormDataProvider implements FormDataProviderInterface
{
    /**
     * @var LogsConfiguration
     */
    private $logsConfiguration;

    public function __construct(LogsConfiguration $logsConfiguration)
    {
        $this->logsConfiguration = $logsConfiguration;
    }

    /**
     * {@inheritdoc}
     */
    public function getData()
    {
        return $this->logsConfiguration->getConfiguration();
    }

    /**
     * {@inheritdoc}
     */
    public function setData(array $data)
    {
        return $this->logsConfiguration->updateConfiguration($data);
    }
}
