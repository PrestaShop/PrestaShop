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
final class MetaSettingsSetUpUrlsFormDataProvider implements FormDataProviderInterface
{
    /**
     * @var DataConfigurationInterface
     */
    private $setUpUrlDataConfiguration;

    /**
     * MetaFormDataProvider constructor.
     *
     * @param DataConfigurationInterface $setUpUrlDataConfiguration
     */
    public function __construct(
        DataConfigurationInterface $setUpUrlDataConfiguration
    ) {
        $this->setUpUrlDataConfiguration = $setUpUrlDataConfiguration;
    }

    /**
     * {@inheritdoc}
     */
    public function getData()
    {
        return $this->setUpUrlDataConfiguration->getConfiguration();
    }

    /**
     * {@inheritdoc}
     */
    public function setData(array $data)
    {
        return $this->setUpUrlDataConfiguration->updateConfiguration($data);
    }
}
