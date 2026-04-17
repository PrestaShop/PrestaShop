<?php

/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Tag\Query;

use PrestaShop\PrestaShop\Core\Domain\Tag\ValueObject\TagId;

class GetTagForEditing
{
    private TagId $tagId;

    public function __construct(int $tagId)
    {
        $this->tagId = new TagId($tagId);
    }

    public function getTagId(): TagId
    {
        return $this->tagId;
    }
}
