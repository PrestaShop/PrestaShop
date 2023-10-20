<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace PrestaShop\PrestaShop\Adapter\Shop;

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
     * @var \Context
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
