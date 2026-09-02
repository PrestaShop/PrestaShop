<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\CmsPageCategory\QueryResult;

use ArrayIterator;
use IteratorAggregate;
use Traversable;

/**
 * Transfers CMS Page Categories used for breadrumb
 */
class Breadcrumb implements IteratorAggregate
{
    /**
     * @var BreadcrumbItem[]
     */
    private $cmsPageCategories;

    /**
     * @param BreadcrumbItem[] $cmsPageCategories
     */
    public function __construct(array $cmsPageCategories)
    {
        $this->cmsPageCategories = $cmsPageCategories;
    }

    /**
     * {@inheritdoc}
     */
    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->cmsPageCategories);
    }
}
