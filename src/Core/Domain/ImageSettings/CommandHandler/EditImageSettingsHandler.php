<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\ImageSettings\CommandHandler;

use PrestaShop\PrestaShop\Adapter\Admin\ImageConfiguration;
use PrestaShop\PrestaShop\Adapter\Domain\AbstractObjectModelHandler;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\ImageSettings\Command\EditImageSettingsCommand;
use PrestaShop\PrestaShop\Core\Domain\ImageSettings\Exception\ImageTypeException;

#[AsCommandHandler]
final class EditImageSettingsHandler extends AbstractObjectModelHandler implements EditImageSettingsHandlerInterface
{
    public function __construct(
        private readonly ImageConfiguration $imageConfiguration
    ) {
    }

    /**
     * {@inheritdoc}
     *
     * @throws ImageTypeException
     */
    public function handle(EditImageSettingsCommand $command): void
    {
        $this->imageConfiguration->updateConfiguration([
            'formats' => $command->getFormats(),
            'base-format' => $command->getBaseFormat(),
            'avif-quality' => $command->getAvifQuality(),
            'jpeg-quality' => $command->getJpegQuality(),
            'png-quality' => $command->getPngQuality(),
            'webp-quality' => $command->getWebpQuality(),
            'generation-method' => $command->getGenerationMethod(),
            'picture-max-size' => $command->getPictureMaxSize(),
            'picture-max-width' => $command->getPictureMaxWidth(),
            'picture-max-height' => $command->getPictureMaxHeight(),
        ]);
    }
}
