<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Product\QueryResult;

/**
 * Contains information about product categories
 */
class CategoriesInformation
{
    /**
     * @var int
     */
    private $defaultCategoryId;

    /**
     * @var CategoryInformation[]
     */
    private $categoriesInformation;

    /**
     * @param CategoryInformation[] $categoriesInformation
     * @param int $defaultCategoryId
     */
    public function __construct(array $categoriesInformation, int $defaultCategoryId)
    {
        $this->categoriesInformation = $categoriesInformation;
        $this->defaultCategoryId = $defaultCategoryId;
    }

    /**
     * @return CategoryInformation[]
     */
    public function getCategoriesInformation(): array
    {
        return $this->categoriesInformation;
    }

    /**
     * @return int
     */
    public function getDefaultCategoryId(): int
    {
        return $this->defaultCategoryId;
    }
}
