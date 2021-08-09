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

namespace PrestaShop\PrestaShop\Core\Domain\Category\ValueObject;

use PrestaShop\PrestaShop\Core\Domain\Category\Exception\CategoryException;

/**
 * Stores id for category's menu thumbnail image.
 */
class MenuThumbnailId
{
    /**
     * @var array category is of having maximum of 3 menu thumbnails with defined Ids
     */
    public const ALLOWED_ID_VALUES = [0, 1, 2];

    /**
     * @var int
     */
    private $menuThumbnailId;

    /**
     * @param int $menuThumbnailId
     */
    public function __construct($menuThumbnailId)
    {
        $this->assertMenuThumbnailIsWithinAllowedValueRange($menuThumbnailId);

        $this->menuThumbnailId = $menuThumbnailId;
    }

    /**
     * @return int
     */
    public function getValue()
    {
        return $this->menuThumbnailId;
    }

    /**
     * @param int $menuThumbnailId
     */
    private function assertMenuThumbnailIsWithinAllowedValueRange($menuThumbnailId)
    {
        if (!is_int($menuThumbnailId) || !in_array($menuThumbnailId, self::ALLOWED_ID_VALUES)) {
            throw new CategoryException(sprintf('Category menu  thumbnail id "%s" invalid. Available values are: %s', var_export($menuThumbnailId, true), implode(',', self::ALLOWED_ID_VALUES)));
        }
    }
}
