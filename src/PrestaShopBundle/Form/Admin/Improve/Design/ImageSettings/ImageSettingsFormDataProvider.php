<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\Form\Admin\Improve\Design\ImageSettings;

use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Domain\ImageSettings\Command\EditImageSettingsCommand;
use PrestaShop\PrestaShop\Core\Domain\ImageSettings\Query\GetImageSettingsForEditing;
use PrestaShop\PrestaShop\Core\Domain\ImageSettings\QueryResult\EditableImageSettings;
use PrestaShop\PrestaShop\Core\Form\FormDataProviderInterface;

/**
 * Provides data for image settings form
 */
final class ImageSettingsFormDataProvider implements FormDataProviderInterface
{
    public function __construct(
        private readonly CommandBusInterface $queryBus,
        private readonly CommandBusInterface $commandBus,
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public function getData(): array
    {
        /** @var EditableImageSettings $settings */
        $settings = $this->queryBus->handle(new GetImageSettingsForEditing());

        return [
            'formats' => $settings->getFormats(),
            'base-format' => $settings->getBaseFormat(),
            'avif-quality' => $settings->getAvifQuality(),
            'jpeg-quality' => $settings->getJpegQuality(),
            'png-quality' => $settings->getPngQuality(),
            'webp-quality' => $settings->getWebpQuality(),
            'generation-method' => $settings->getGenerationMethod(),
            'picture-max-size' => $settings->getPictureMaxSize(),
            'picture-max-width' => $settings->getPictureMaxWidth(),
            'picture-max-height' => $settings->getPictureMaxHeight(),
        ];
    }

    public function setData(array $data)
    {
        $command = new EditImageSettingsCommand();
        $command->setFormats($data['formats']);
        $command->setBaseFormat($data['base-format']);
        $command->setAvifQuality($data['avif-quality']);
        $command->setJpegQuality($data['jpeg-quality']);
        $command->setPngQuality($data['png-quality']);
        $command->setWebpQuality($data['webp-quality']);
        $command->setGenerationMethod($data['generation-method']);
        $command->setPictureMaxSize($data['picture-max-size']);
        $command->setPictureMaxWidth($data['picture-max-width']);
        $command->setPictureMaxHeight($data['picture-max-height']);
        $this->commandBus->handle($command);

        return [];
    }
}
