<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Form\Admin\Sell\Order\CreditSlip;

use PrestaShop\PrestaShop\Core\Configuration\DataConfigurationInterface;
use PrestaShop\PrestaShop\Core\Form\FormDataProviderInterface;

/**
 * Provides data for credit slip options form
 */
final class CreditSlipOptionsFormDataProvider implements FormDataProviderInterface
{
    /**
     * @var DataConfigurationInterface
     */
    private $creditSlipOptionsConfiguration;

    /**
     * @param DataConfigurationInterface $creditSlipOptionsConfiguration
     */
    public function __construct(DataConfigurationInterface $creditSlipOptionsConfiguration)
    {
        $this->creditSlipOptionsConfiguration = $creditSlipOptionsConfiguration;
    }

    /**
     * {@inheritdoc}
     */
    public function getData()
    {
        return $this->creditSlipOptionsConfiguration->getConfiguration();
    }

    /**
     * {@inheritdoc}
     */
    public function setData(array $data)
    {
        return $this->creditSlipOptionsConfiguration->updateConfiguration($data);
    }
}
