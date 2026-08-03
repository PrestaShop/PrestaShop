<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Import\Engine\Repository;

use Doctrine\DBAL\Connection;
use PrestaShop\PrestaShop\Core\ConfigurationInterface;

/**
 * @internal only meant for internal use by the Import engine components,
 *           not to be overridden or decorated
 */
final class CategoryLookup
{
    public function __construct(
        private readonly Connection $connection,
        private readonly string $dbPrefix,
        private readonly ConfigurationInterface $configuration,
    ) {
    }

    public function categoryExists(int $categoryId): bool
    {
        return false !== $this->connection->fetchOne(
            'SELECT 1 FROM ' . $this->dbPrefix . 'category WHERE id_category = :categoryId',
            ['categoryId' => $categoryId]
        );
    }

    /**
     * Direct child of $parentCategoryId whose name matches in the given
     * language (legacy Category::searchByNameAndParentCategoryId parity).
     */
    public function getChildCategoryIdByName(int $parentCategoryId, string $name, int $languageId): ?int
    {
        $categoryId = $this->connection->fetchOne(
            'SELECT c.id_category
            FROM ' . $this->dbPrefix . 'category c
            INNER JOIN ' . $this->dbPrefix . 'category_lang cl
                ON cl.id_category = c.id_category AND cl.id_lang = :languageId
            WHERE cl.name = :name AND c.id_parent = :parentCategoryId
            ORDER BY c.id_category ASC',
            ['languageId' => $languageId, 'name' => $name, 'parentCategoryId' => $parentCategoryId]
        );

        return false === $categoryId ? null : (int) $categoryId;
    }

    public function getHomeCategoryId(): int
    {
        return (int) $this->configuration->get('PS_HOME_CATEGORY');
    }
}
