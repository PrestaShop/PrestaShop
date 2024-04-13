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

namespace PrestaShop\PrestaShop\Core\Domain\ImageSettings\CommandHandler;

use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\AbstractBulkCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\ImageSettings\Command\BulkDeleteImageTypeCommand;
use PrestaShop\PrestaShop\Core\Domain\ImageSettings\Exception\BulkImageTypeException;
use PrestaShop\PrestaShop\Core\Domain\ImageSettings\Exception\ImageTypeException;
use PrestaShop\PrestaShop\Core\Domain\ImageSettings\Exception\ImageTypeNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\ImageSettings\ValueObject\ImageTypeId;
use PrestaShopBundle\Entity\Repository\ImageTypeRepository;

/**
 * Handles command that bulk delete image types
 */
#[AsCommandHandler]
class BulkDeleteImageTypeHandler extends AbstractBulkCommandHandler implements BulkDeleteImageTypeHandlerInterface
{
    public function __construct(
        private readonly ImageTypeRepository $imageTypeRepository
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public function handle(BulkDeleteImageTypeCommand $command): void
    {
        $this->handleBulkAction($command->getImageTypeIds(), ImageTypeException::class);
    }

    protected function buildBulkException(array $caughtExceptions): BulkImageTypeException
    {
        return new BulkImageTypeException(
            $caughtExceptions,
            'Errors occurred during image type bulk delete action',
        );
    }

    /**
     * @param ImageTypeId $id
     * @param mixed $command
     *
     * @return void
     *
     * @throws ImageTypeNotFoundException
     */
    protected function handleSingleAction(mixed $id, mixed $command): void
    {
        $imageType = $this->imageTypeRepository->find($id->getValue());

        if (null === $imageType) {
            throw new ImageTypeNotFoundException(sprintf('Unable to find image type with id "%d" for deletion', $id->getValue()));
        }

        $this->imageTypeRepository->delete($imageType);
    }

    protected function supports($id): bool
    {
        return $id instanceof ImageTypeId;
    }
}
