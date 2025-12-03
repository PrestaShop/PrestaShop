<?php

/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
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
