<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Tag\CommandHandler;

use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\Tag\Command\BulkDeleteTagCommand;
use PrestaShop\PrestaShop\Core\Domain\Tag\CommandHandler\BulkDeleteTagHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Tag\Exception\TagNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Tag\ValueObject\TagId;
use Tag;

/**
 * Handles command that bulk delete tags
 */
#[AsCommandHandler]
class BulkDeleteTagHandler implements BulkDeleteTagHandlerInterface
{
    /**
     * {@inheritdoc}
     */
    public function handle(BulkDeleteTagCommand $command): void
    {
        foreach ($command->getTagIds() as $tagId) {
            $tag = $this->getLegacyTag($tagId);
            $tag->delete();
        }
    }

    /**
     * @param TagId $tagId
     *
     * @return Tag
     */
    protected function getLegacyTag(TagId $tagId): Tag
    {
        $tag = new Tag($tagId->getValue());

        if ($tagId->getValue() !== $tag->id) {
            throw new TagNotFoundException(sprintf('Tag with id "%s was not found', $tagId->getValue()));
        }

        return $tag;
    }
}
