<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Tag\CommandHandler;

use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\Tag\Command\DeleteTagCommand;
use PrestaShop\PrestaShop\Core\Domain\Tag\CommandHandler\DeleteTagHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Tag\Exception\TagNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Tag\ValueObject\TagId;
use Tag;

/**
 * Handles command that delete tag
 */
#[AsCommandHandler]
class DeleteTagHandler implements DeleteTagHandlerInterface
{
    /**
     * {@inheritdoc}
     */
    public function handle(DeleteTagCommand $command): void
    {
        $tag = $this->getLegacyTag($command->getTagId());
        $tag->delete();
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
