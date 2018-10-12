<?php
/**
 * 2007-2018 PrestaShop.
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
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Adapter\CMS\PageCategory\QueryHandler;

use CMSCategory;
use Doctrine\DBAL\Connection;
use PrestaShop\PrestaShop\Core\Domain\CmsPageCategory\CmsPageRootCategorySettings;
use PrestaShop\PrestaShop\Core\Domain\CmsPageCategory\Exception\CmsPageCategoryException;
use PrestaShop\PrestaShop\Core\Domain\CmsPageCategory\Query\GetCmsPageCategoriesForBreadcrumbs;
use PrestaShop\PrestaShop\Core\Domain\CmsPageCategory\QueryHandler\GetCmsPageCategoriesForBreadcrumbsHandlerInterface;
use PrestaShopException;

/**
 * Class GetCmsPageCategoriesForBreadcrumbsHandler
 */
final class GetCmsPageCategoriesForBreadcrumbsHandler implements GetCmsPageCategoriesForBreadcrumbsHandlerInterface
{
    /**
     * @var Connection
     */
    private $connection;

    /**
     * @var string
     */
    private $dbPrefix;

    public function __construct(Connection $connection, $dbPrefix)
    {
        $this->connection = $connection;
        $this->dbPrefix = $dbPrefix;
    }

    /**
     * {@inheritdoc}
     *
     * @throws CmsPageCategoryException
     */
    public function handle(GetCmsPageCategoriesForBreadcrumbs $query)
    {
        try {
            $currentCategory = new CMSCategory($query->getCurrentCategoryId()->getValue());
        } catch (PrestaShopException $exception) {
            throw new CmsPageCategoryException(
                sprintf(
                    'An error occurred when finding cms category object with id "%s"',
                    $query->getCurrentCategoryId()->getValue()
                ),
                0,
                $exception
            );
        }

        // todo : restrict to shop ids
        $qb = $this->connection->createQueryBuilder()
            ->select('DISTINCT IF(0 = `id_parent`, `id_cms_category`, `id_parent`) AS `id_cms_page_category`')
            ->from($this->dbPrefix . 'cms_category')
            ->where('`id_parent` <= :currentCategoryParentId')
            ->groupBy('`id_parent`')
        ;

        $qb->setParameters(['currentCategoryParentId' => $currentCategory->id_parent]);
    }
}
