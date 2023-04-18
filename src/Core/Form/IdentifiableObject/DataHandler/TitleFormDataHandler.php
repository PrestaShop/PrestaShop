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

namespace PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataHandler;

use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Domain\Title\Command\AddTitleCommand;
use PrestaShop\PrestaShop\Core\Domain\Title\Command\EditTitleCommand;
use PrestaShop\PrestaShop\Core\Domain\Title\ValueObject\TitleId;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Handles submitted title form data
 */
class TitleFormDataHandler implements FormDataHandlerInterface
{
    /**
     * @var CommandBusInterface
     */
    private $commandBus;

    /**
     * @param CommandBusInterface $commandBus
     */
    public function __construct(CommandBusInterface $commandBus)
    {
        $this->commandBus = $commandBus;
    }

    /**
     * {@inheritdoc}
     */
    public function create(array $data): int
    {
        /** @var UploadedFile|null $uploadedImage */
        $uploadedImage = $data['image'];

        /** @var TitleId $titleId */
        $titleId = $this->commandBus->handle(new AddTitleCommand(
            $data['name'],
            (int) $data['gender_type'],
            $uploadedImage,
            $data['img_width'],
            $data['img_height']
        ));

        return $titleId->getValue();
    }

    /**
     * {@inheritdoc}
     */
    public function update($id, array $data): void
    {
        $command = new EditTitleCommand((int) $id);
        $command
            ->setLocalizedNames($data['name'])
            ->setGender((int) $data['gender_type']);

        /** @var UploadedFile|null $uploadedImage */
        $uploadedImage = $data['image'];
        if ($uploadedImage instanceof UploadedFile) {
            $command
                ->setImageFile($uploadedImage)
                ->setImageWidth($data['img_width'])
                ->setImageHeight($data['img_height']);
        }

        $this->commandBus->handle($command);
    }
}
