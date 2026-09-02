<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Form\Admin\Improve\Payment\Preferences;

use PrestaShop\PrestaShop\Core\Configuration\DataConfigurationInterface;
use PrestaShop\PrestaShop\Core\Form\FormDataProviderInterface;

/**
 * Class PaymentPreferencesFormDataProvider is responsible for handling "Improve > Payment > Preferences" form data.
 */
final class PaymentPreferencesFormDataProvider implements FormDataProviderInterface
{
    /**
     * @var DataConfigurationInterface
     */
    private $paymentModulePreferencesConfiguration;

    /**
     * @param DataConfigurationInterface $paymentModulePreferencesConfiguration
     */
    public function __construct(
        DataConfigurationInterface $paymentModulePreferencesConfiguration
    ) {
        $this->paymentModulePreferencesConfiguration = $paymentModulePreferencesConfiguration;
    }

    /**
     * {@inheritdoc}
     */
    public function getData()
    {
        return $this->paymentModulePreferencesConfiguration->getConfiguration();
    }

    /**
     * {@inheritdoc}
     */
    public function setData(array $data)
    {
        return $this->paymentModulePreferencesConfiguration->updateConfiguration($data);
    }
}
