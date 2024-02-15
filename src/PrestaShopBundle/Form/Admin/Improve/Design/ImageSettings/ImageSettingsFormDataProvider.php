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
