<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
