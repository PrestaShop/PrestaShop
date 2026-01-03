<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Twig;

use PrestaShop\PrestaShop\Core\ConfigurationInterface;
use PrestaShop\PrestaShop\Core\Context\ShopContext;
use Twig\Extension\AbstractExtension;
use Twig\Extension\GlobalsInterface;

final class TwoFactorGlobalsExtension extends AbstractExtension implements GlobalsInterface
{
    private ShopContext $shopContext;
    private ConfigurationInterface $configuration;

    public function __construct(
        ShopContext $shopContext,
        ConfigurationInterface $configuration
    ) {
        $this->shopContext = $shopContext;
        $this->configuration = $configuration;
    }

    public function getGlobals(): array
    {
        $imgDir = rtrim($this->shopContext->getBaseURI(), '/') . '/img/';

        return [
            'imgDir' => $imgDir,
            'shopName' => (string) $this->configuration->get('PS_SHOP_NAME'),
        ];
    }
}
