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

namespace PrestaShop\PrestaShop\Adapter\Title\CommandHandler;

use Gender;
use PrestaShop\PrestaShop\Adapter\Title\AbstractTitleHandler;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\Title\Command\EditTitleCommand;
use PrestaShop\PrestaShop\Core\Domain\Title\CommandHandler\EditTitleHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Title\Exception\CannotUpdateTitleException;
use PrestaShop\PrestaShop\Core\Domain\Title\Exception\TitleImageUploadingException;

/**
 * Handles edition of title
 */
#[AsCommandHandler]
class EditTitleHandler extends AbstractTitleHandler implements EditTitleHandlerInterface
{
    /**
     * {@inheritdoc}
     */
    public function handle(EditTitleCommand $command): void
    {
        $title = $this->titleRepository->get($command->getTitleId());
        $updatableProperties = [];
        if (null !== $command->getLocalizedNames()) {
            $title->name = $command->getLocalizedNames();
            $updatableProperties['name'] = array_keys($command->getLocalizedNames());
        }
        if (null !== $command->getGender()) {
            $title->type = $command->getGender()->getValue();
            $updatableProperties[] = 'type';
        }

        $this->titleRepository->partialUpdate(
            $title,
            $updatableProperties,
            CannotUpdateTitleException::FAILED_UPDATE_TITLE
        );

        $this->uploadTitleImage($title, $command);
    }

    /**
     * Update title's flag image if it has changed
     *
     * @param Gender $title
     * @param EditTitleCommand $command
     *
     * @throws TitleImageUploadingException
     */
    private function uploadTitleImage(Gender $title, EditTitleCommand $command): void
    {
        if (!$command->getImageFile()) {
            return;
        }

        $this->titleImageUploader->upload(
            (int) $title->id,
            $command->getImageFile(),
            $command->getImageWidth(),
            $command->getImageHeight()
        );
    }
}
