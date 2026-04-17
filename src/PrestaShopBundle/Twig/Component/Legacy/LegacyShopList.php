<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\Twig\Component\Legacy;

use HelperShop;
use PrestaShop\PrestaShop\Adapter\Feature\MultistoreFeature;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(template: '@PrestaShop/Admin/Component/LegacyLayout/shop_list.html.twig')]
class LegacyShopList
{
    protected ?string $renderedShops = null;

    public function __construct(
        protected readonly MultistoreFeature $multistoreFeature
    ) {
    }

    public function getShopList(): ?string
    {
        if (!$this->multistoreFeature->isUsed()) {
            return null;
        }

        if (!$this->renderedShops) {
            $helperShop = new HelperShop();

            $this->renderedShops = $helperShop->getRenderedShopList();
        }

        return $this->renderedShops;
    }
}
