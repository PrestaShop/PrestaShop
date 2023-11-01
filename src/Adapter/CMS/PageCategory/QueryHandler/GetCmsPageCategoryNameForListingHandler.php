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

namespace PrestaShop\PrestaShop\Adapter\CMS\PageCategory\QueryHandler;

use CMSCategory;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsQueryHandler;
use PrestaShop\PrestaShop\Core\Domain\CmsPageCategory\Query\GetCmsPageCategoryNameForListing;
use PrestaShop\PrestaShop\Core\Domain\CmsPageCategory\QueryHandler\GetCmsPageCategoryNameForListingHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\CmsPageCategory\ValueObject\CmsPageCategoryId;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Gets name by cms category which are used for display in cms listing.
 */
#[AsQueryHandler]
final class GetCmsPageCategoryNameForListingHandler implements GetCmsPageCategoryNameForListingHandlerInterface
{
    /**
     * @var int
     */
    private $contextLanguageId;

    /**
     * @var RequestStack
     */
    private $requestStack;

    /**
     * @param int $contextLanguageId
     * @param RequestStack $requestStack
     */
    public function __construct(
        $contextLanguageId,
        RequestStack $requestStack
    ) {
        $this->contextLanguageId = $contextLanguageId;
        $this->requestStack = $requestStack;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(GetCmsPageCategoryNameForListing $query)
    {
        $cmsCategory = new CMSCategory($this->getCmsCategoryIdFromRequest());

        return isset($cmsCategory->name[$this->contextLanguageId]) ? $cmsCategory->name[$this->contextLanguageId] : '';
    }

    /**
     * Gets id from request or fall-backs to the default one if not found.
     *
     * @return int
     */
    private function getCmsCategoryIdFromRequest()
    {
        $currentRequest = $this->requestStack->getCurrentRequest();
        $categoryIdFromRequest = null;
        if (null !== $currentRequest) {
            $categoryIdFromRequest = $currentRequest->query->getInt('id_cms_category');
        }

        return $categoryIdFromRequest ?: CmsPageCategoryId::ROOT_CMS_PAGE_CATEGORY_ID;
    }
}
