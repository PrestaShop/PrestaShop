<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Category\QueryResult;

/**
 * Minimum data to display a preview of a category
 */
class CategoryPreview
{
    /**
     * @var int
     */
    private $categoryId;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $breadcrumb;

    /**
     * @var string
     */
    private $image;

    /**
     * @param int $categoryId
     * @param string $name
     * @param string $breadcrumb
     * @param string $imageUrl
     */
    public function __construct(
        int $categoryId,
        string $name,
        string $breadcrumb,
        string $imageUrl
    ) {
        $this->categoryId = $categoryId;
        $this->name = $name;
        $this->breadcrumb = $breadcrumb;
        $this->image = $imageUrl;
    }

    /**
     * @return int
     */
    public function getCategoryId(): int
    {
        return $this->categoryId;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getBreadcrumb(): string
    {
        return $this->breadcrumb;
    }

    /**
     * @return string
     */
    public function getImage(): string
    {
        return $this->image;
    }
}
