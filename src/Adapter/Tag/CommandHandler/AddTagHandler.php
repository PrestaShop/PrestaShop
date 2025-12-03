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
