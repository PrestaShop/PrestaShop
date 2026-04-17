<?php

/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Tag\CommandHandler;

use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\Tag\Command\AddTagCommand;
use PrestaShop\PrestaShop\Core\Domain\Tag\CommandHandler\AddTagCommandHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Tag\Exception\CannotAddTagException;
use PrestaShop\PrestaShop\Core\Domain\Tag\Exception\DuplicateTagException;
use PrestaShop\PrestaShop\Core\Domain\Tag\Exception\TagConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Tag\ValueObject\TagId;
use Tag;
use Validate;

#[AsCommandHandler]
class AddTagHandler implements AddTagCommandHandlerInterface
{
    public function handle(AddTagCommand $command): TagId
    {
        $this->assertTagIsNotDuplicate($command);

        $tag = $this->createLegacyTagFromCommand($command);

        return new TagId((int) $tag->id);
    }

    /**
     * Asserts that new tag does not duplicate already tags
     */
    protected function assertTagIsNotDuplicate(AddTagCommand $command)
    {
        $tag = new Tag(null, $command->getName(), (int) $command->getLanguageId());
        if (Validate::isLoadedObject($tag)) {
            throw new DuplicateTagException(sprintf('Tag "%s" already exists', $command->getName()));
        }
    }

    /**
     * @param AddTagCommand $command
     *
     * @return Tag
     */
    protected function createLegacyTagFromCommand(AddTagCommand $command): Tag
    {
        $tag = new Tag();
        $tag->name = $command->getName();
        $tag->id_lang = (int) $command->getLanguageId();

        if (false === $tag->validateFields(false)) {
            throw new TagConstraintException('One or more fields are invalid in Tag');
        }

        if (false === $tag->add()) {
            throw new CannotAddTagException('Failed to add Tag');
        }

        $productIds = $command->getProductIds();
        if (!empty($productIds)) {
            $tag->setProducts($productIds);
        }

        return $tag;
    }
}
