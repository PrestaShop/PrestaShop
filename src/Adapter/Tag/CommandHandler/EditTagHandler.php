<?php

/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Tag\CommandHandler;

use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\Tag\Command\EditTagCommand;
use PrestaShop\PrestaShop\Core\Domain\Tag\CommandHandler\EditTagCommandHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Tag\Exception\CannotUpdateTagException;
use PrestaShop\PrestaShop\Core\Domain\Tag\Exception\TagConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Tag\Exception\TagNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Tag\ValueObject\TagId;
use Tag;

#[AsCommandHandler]
class EditTagHandler implements EditTagCommandHandlerInterface
{
    /**
     * {@inheritdoc}
     */
    public function handle(EditTagCommand $command): void
    {
        $tag = $this->getLegacyTag($command->getTagId());

        $this->updateLegacyTagWithCommandData($tag, $command);
    }

    /**
     * @param TagId $tagId
     *
     * @return Tag
     */
    private function getLegacyTag(TagId $tagId): Tag
    {
        $tag = new Tag($tagId->getValue());

        if ($tagId->getValue() !== $tag->id) {
            throw new TagNotFoundException(sprintf('Tag with id "%s was not found', $tagId->getValue()));
        }

        return $tag;
    }

    protected function updateLegacyTagWithCommandData(
        Tag $tag,
        EditTagCommand $command
    ): void {
        if (null !== $command->getName()) {
            $tag->name = $command->getName();
        }

        if (null !== $command->getLanguageId()) {
            $tag->id_lang = $command->getLanguageId();
        }

        if (false === $tag->validateFields(false)) {
            throw new TagConstraintException('One or more fields are invalid in Tag');
        }

        if (false === $tag->update()) {
            throw new CannotUpdateTagException(sprintf('Failed to update Tag with id "%s"', $tag->id));
        }

        if (null !== $command->getProductIds()) {
            $tag->setProducts($command->getProductIds());
        }
    }
}
