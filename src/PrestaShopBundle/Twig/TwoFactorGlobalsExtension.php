<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Twig;

use PrestaShop\PrestaShop\Core\ConfigurationInterface;
use Twig\Extension\AbstractExtension;
use Twig\Extension\GlobalsInterface;

final class TwoFactorGlobalsExtension extends AbstractExtension implements GlobalsInterface
{
    public function __construct(
        private readonly ConfigurationInterface $configuration
    ) {
    }

    public function getGlobals(): array
    {
        return [
            'shopName' => (string) $this->configuration->get('PS_SHOP_NAME'),
        ];
    }
}
