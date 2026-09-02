<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Form\Admin\Improve\International\Localization;

use PrestaShop\PrestaShop\Core\Configuration\DataConfigurationInterface;
use PrestaShop\PrestaShop\Core\Form\FormDataProviderInterface;

/**
 * Class LocalizationFormDataProvider is responsible for handling
 * 'Improve > International > Localization' page forms data.
 */
class LocalizationFormDataProvider implements FormDataProviderInterface
{
    /**
     * @var DataConfigurationInterface
     */
    private $dataConfiguration;

    /**
     * @param DataConfigurationInterface $dataConfiguration
     */
    public function __construct(
        DataConfigurationInterface $dataConfiguration
    ) {
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
