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
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Grid\Action\Row\AccessibilityChecker;

use PrestaShop\PrestaShop\Core\Multistore\MultistoreContextCheckerInterface;

/**
 * This checker is used in multishop view, some actions should not be displayed for products
 * associated to multiple shops. If there is only one it's ok though.
 */
class ProductSingleShopAssociatedAccessibilityChecker implements AccessibilityCheckerInterface
{
    /**
     * @var MultistoreContextCheckerInterface
     */
    private $multiStoreContext;

    public function __construct(
        MultistoreContextCheckerInterface $multiStoreContext
    ) {
        $this->multiStoreContext = $multiStoreContext;
    }

    /**
     * @param array{associated_shops_ids?: array<int>} $record
     *
     * @return bool
     */
    public function isGranted(array $record)
    {
        if ($this->multiStoreContext->isSingleShopContext()) {
            return true;
        }

        return empty($record['associated_shops_ids']) || count($record['associated_shops_ids']) === 1;
    }
}
