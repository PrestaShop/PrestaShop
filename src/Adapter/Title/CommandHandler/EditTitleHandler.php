<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
