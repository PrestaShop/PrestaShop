<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Tag\Command;

use PrestaShop\PrestaShop\Core\Domain\Tag\ValueObject\TagId;

/**
 * Delete tag
 */
class DeleteTagCommand
{
    /**
     * @var TagId
     */
    private $tagId;

    /**
     * @param int $tagId
     */
    public function __construct(int $tagId)
    {
        $this->tagId = new TagId($tagId);
    }

    /**
     * @return TagId
     */
    public function getTagId(): TagId
    {
        return $this->tagId;
    }
}
