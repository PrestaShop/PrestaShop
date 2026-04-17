<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Tag\Command;

use PrestaShop\PrestaShop\Core\Domain\Tag\ValueObject\TagId;

/**
 * Deletes tag on bulk action
 */
class BulkDeleteTagCommand
{
    /**
     * @var array<int, TagId>
     */
    private $tagIds;

    /**
     * @param array<int, int> $tagIds
     */
    public function __construct(array $tagIds)
    {
        $this->setTagIds($tagIds);
    }

    /**
     * @return array<int, TagId>
     */
    public function getTagIds(): array
    {
        return $this->tagIds;
    }

    /**
     * @param array<int, int> $tagIds
     */
    private function setTagIds(array $tagIds): void
    {
        foreach ($tagIds as $tagId) {
            $this->tagIds[] = new TagId((int) $tagId);
        }
    }
}
