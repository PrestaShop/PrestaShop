<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Shop;

use Context;
use PrestaShop\PrestaShop\Adapter\LegacyContext;
use PrestaShop\PrestaShop\Core\Version;
use Tools;

/**
 * Retrieve common information from a the actual Shop.
 *
 * Depends on Context, avoid re-use of this class
 */
class ShopInformation
{
    /**
     * @var Context
     */
    private $context;

    /**
     * @param LegacyContext $legacyContext
     */
    public function __construct(LegacyContext $legacyContext)
    {
        $this->context = $legacyContext->getContext();
    }

    /**
     * @return array
     */
    public function getShopInformation()
    {
        return [
            'version' => Version::VERSION,
            'url' => $this->context->shop->getBaseURL(),
            'path' => _PS_ROOT_DIR_,
            'theme' => $this->context->shop->theme->getName(),
        ];
    }

    /**
     * @return array
     */
    public function getOverridesList(): array
    {
        return array_filter(Tools::scandir(_PS_OVERRIDE_DIR_, 'php', '', true), function ($file) {
            return basename($file) != 'index.php';
        });
    }
}
