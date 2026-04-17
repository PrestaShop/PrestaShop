<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Form\Admin\Configure\AdvancedParameters\Backup;

use PrestaShop\PrestaShop\Core\Configuration\DataConfigurationInterface;
use PrestaShop\PrestaShop\Core\Form\FormDataProviderInterface;

/**
 * Class BackupDataProvider provides backup options form data.
 */
final class BackupDataProvider implements FormDataProviderInterface
{
    /**
     * @var DataConfigurationInterface
     */
    private $backupOptionsConfigurator;

    /**
     * @param DataConfigurationInterface $backupOptionsConfigurator
     */
    public function __construct(DataConfigurationInterface $backupOptionsConfigurator)
    {
        $this->backupOptionsConfigurator = $backupOptionsConfigurator;
    }

    /**
     * {@inheritdoc}
     */
    public function getData()
    {
        return $this->backupOptionsConfigurator->getConfiguration();
    }

    /**
     * {@inheritdoc}
     */
    public function setData(array $data)
    {
        return $this->backupOptionsConfigurator->updateConfiguration($data);
    }
}
