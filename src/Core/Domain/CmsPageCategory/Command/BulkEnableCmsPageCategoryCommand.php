<?php
/**
 * 2007-2019 PrestaShop and Contributors
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Core\Domain\CmsPageCategory\Command;

use PrestaShop\PrestaShop\Core\Domain\CmsPageCategory\Exception\CmsPageCategoryConstraintException;
use PrestaShop\PrestaShop\Core\Domain\CmsPageCategory\Exception\CmsPageCategoryException;
use PrestaShop\PrestaShop\Core\Domain\CmsPageCategory\ValueObject\CmsPageCategoryId;

/**
 * Class BulkEnableCmsPageCategoryCommand is responsible for enabling cms category pages.
 */
class BulkEnableCmsPageCategoryCommand extends AbstractBulkCmsPageCategoryCommand
{
    /**
     * @var CmsPageCategoryId[]
     */
    private $cmsPageCategoryIds;

    /**
     * @param int[] $cmsPageCategoryIds
     *
     * @throws CmsPageCategoryException
     */
    public function __construct(array $cmsPageCategoryIds)
    {
        if ($this->assertIsEmptyOrContainsNonIntegerValues($cmsPageCategoryIds)) {
            throw new CmsPageCategoryConstraintException(
                sprintf(
                    'Missing cms page category data or array %s contains non integer values for bulk enabling',
                    var_export($cmsPageCategoryIds, true)
                ),
                CmsPageCategoryConstraintException::INVALID_BULK_DATA
            );
        }

        $this->setCmsPageCategoryIds($cmsPageCategoryIds);
    }

    /**
     * @return CmsPageCategoryId[]
     */
    public function getCmsPageCategoryIds()
    {
        return $this->cmsPageCategoryIds;
    }

    /**
     * @param int[] $cmsPageCategoryIds
     *
     * @throws CmsPageCategoryException
     */
    private function setCmsPageCategoryIds(array $cmsPageCategoryIds)
    {
        foreach ($cmsPageCategoryIds as $id) {
            $this->cmsPageCategoryIds[] = new CmsPageCategoryId($id);
        }
    }
}
