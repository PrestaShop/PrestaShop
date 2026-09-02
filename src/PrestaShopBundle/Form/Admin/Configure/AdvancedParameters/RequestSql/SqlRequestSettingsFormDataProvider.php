<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Form\Admin\Configure\AdvancedParameters\RequestSql;

use PrestaShop\PrestaShop\Core\Configuration\DataConfigurationInterface;
use PrestaShop\PrestaShop\Core\Form\FormDataProviderInterface;

/**
 * Class RequestSqlSettingsFormDataProvider is responsible for managing RequestSql settings.
 */
final class SqlRequestSettingsFormDataProvider implements FormDataProviderInterface
{
    /**
     * @var DataConfigurationInterface
     */
    private $dataConfiguration;

    /**
     * @param DataConfigurationInterface $dataConfiguration
     */
    public function __construct(DataConfigurationInterface $dataConfiguration)
    {
        $this->dataConfiguration = $dataConfiguration;
    }

    /**
     * {@inheritdoc}
     */
    public function getData()
    {
        return $this->dataConfiguration->getConfiguration();
    }

    /**
     * {@inheritdoc}
     */
    public function setData(array $data)
    {
        return $this->dataConfiguration->updateConfiguration($data);
    }
}
