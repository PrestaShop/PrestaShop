<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Form\Admin\Sell\Order\Delivery;

use PrestaShop\PrestaShop\Adapter\Order\Delivery\SlipOptionsConfiguration;
use PrestaShop\PrestaShop\Core\Form\FormDataProviderInterface;

/**
 * This class is responsible of managing the data manipulated options form
 * in "Sells > Orders > Delivery Slips" page.
 */
final class SlipOptionsFormDataProvider implements FormDataProviderInterface
{
    /**
     * @var SlipOptionsConfiguration
     */
    private $configuration;

    public function __construct(SlipOptionsConfiguration $configuration)
    {
        $this->configuration = $configuration;
    }

    /**
     * {@inheritdoc}
     */
    public function getData()
    {
        return $this->configuration->getConfiguration();
    }

    /**
     * {@inheritdoc}
     */
    public function setData(array $data)
    {
        return $this->configuration->updateConfiguration($data);
    }
}
