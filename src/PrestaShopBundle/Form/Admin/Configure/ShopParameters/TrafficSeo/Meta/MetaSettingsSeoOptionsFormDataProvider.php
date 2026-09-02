<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace PrestaShopBundle\Form\Admin\Configure\ShopParameters\TrafficSeo\Meta;

use PrestaShop\PrestaShop\Core\Configuration\DataConfigurationInterface;
use PrestaShop\PrestaShop\Core\Form\FormDataProviderInterface;

/**
 * Class MetaSettingsFormDataProvider is responsible for providing configurations data and responsible for persisting data
 * in configuration database.
 */
final class MetaSettingsSeoOptionsFormDataProvider implements FormDataProviderInterface
{
    /**
     * @var DataConfigurationInterface
     */
    private $seoOptionsDataConfiguration;

    /**
     * MetaFormDataProvider constructor.
     *
     * @param DataConfigurationInterface $seoOptionsDataConfiguration
     */
    public function __construct(
        DataConfigurationInterface $seoOptionsDataConfiguration
    ) {
        $this->seoOptionsDataConfiguration = $seoOptionsDataConfiguration;
    }

    /**
     * {@inheritdoc}
     */
    public function getData()
    {
        return $this->seoOptionsDataConfiguration->getConfiguration();
    }

    /**
     * {@inheritdoc}
     */
    public function setData(array $data)
    {
        return $this->seoOptionsDataConfiguration->updateConfiguration($data);
    }
}
