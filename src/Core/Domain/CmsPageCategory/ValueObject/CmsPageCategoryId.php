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

namespace PrestaShop\PrestaShop\Core\Domain\CmsPageCategory\ValueObject;

use PrestaShop\PrestaShop\Core\Domain\CmsPageCategory\Exception\CmsPageCategoryException;

/**
 * Class CmsPageCategoryId is responsible for providing identificator for cms page category
 */
class CmsPageCategoryId
{
    /**
     * ID for the topmost Cms Page category
     */
    public const ROOT_CMS_PAGE_CATEGORY_ID = 1;

    /**
     * @var int
     */
    private $cmsPageCategoryId;

    /**
     * @param int $cmsPageCategoryId
     *
     * @throws CmsPageCategoryException
     */
    public function __construct($cmsPageCategoryId)
    {
        $this->assertIsIntegerGreaterThanZero($cmsPageCategoryId);
        $this->cmsPageCategoryId = $cmsPageCategoryId;
    }

    /**
     * @return int
     */
    public function getValue()
    {
        return $this->cmsPageCategoryId;
    }

    /**
     * Validates that the value is integer and is greater than zero.
     *
     * @param int $cmsPageCategoryId
     *
     * @throws CmsPageCategoryException
     */
    private function assertIsIntegerGreaterThanZero($cmsPageCategoryId)
    {
        if (!is_int($cmsPageCategoryId) || 0 >= $cmsPageCategoryId) {
            throw new CmsPageCategoryException(sprintf('Invalid cms page category id %s', var_export($cmsPageCategoryId, true)));
        }
    }
}
