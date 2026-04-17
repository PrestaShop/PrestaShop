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
