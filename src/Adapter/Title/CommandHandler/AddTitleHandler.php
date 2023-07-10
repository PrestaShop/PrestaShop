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
use PrestaShop\PrestaShop\Core\Domain\Title\Command\AddTitleCommand;
use PrestaShop\PrestaShop\Core\Domain\Title\CommandHandler\AddTitleHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Title\Exception\TitleException;
use PrestaShop\PrestaShop\Core\Domain\Title\Exception\TitleImageUploadingException;
use PrestaShop\PrestaShop\Core\Domain\Title\ValueObject\TitleId;

/**
 * Handles creation of title
 */
#[AsCommandHandler]
class AddTitleHandler extends AbstractTitleHandler implements AddTitleHandlerInterface
{
    /**
     * {@inheritdoc}
     *
     * @throws TitleException
     */
    public function handle(AddTitleCommand $command): TitleId
    {
        $title = new Gender();
        $title->name = $command->getLocalizedNames();
        $title->type = $command->getGender()->getValue();

        $titleId = $this->titleRepository->add($title);

        $this->uploadTitleImage($titleId, $command);

        return $titleId;
    }

    /**
     * @param TitleId $titleId
     * @param AddTitleCommand $command
     *
     * @return void
     *
     * @throws TitleImageUploadingException
     */
    protected function uploadTitleImage(TitleId $titleId, AddTitleCommand $command): void
    {
        if (!$command->getImageFile()) {
            return;
        }

        $this->titleImageUploader->upload(
            $titleId->getValue(),
            $command->getImageFile(),
            $command->getImageWidth(),
            $command->getImageHeight()
        );
    }
}
