<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace PrestaShopBundle\Form\Admin\Configure\ShopParameters\ProductPreferences;

use PrestaShop\PrestaShop\Adapter\Product\PageConfiguration;
use PrestaShop\PrestaShop\Core\Form\FormDataProviderInterface;

/**
 * Class is responsible of managing the data manipulated using forms
 * in "Configure > Shop Parameters > Product Settings" page.
 */
class PageFormDataProvider implements FormDataProviderInterface
{
    /**
     * @var PageConfiguration
     */
    private $configuration;

    public function __construct(
        PageConfiguration $configuration
    ) {
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
