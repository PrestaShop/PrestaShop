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

namespace PrestaShop\PrestaShop\Adapter\ImageType\CommandHandler;

use PrestaShop\PrestaShop\Adapter\ImageType\AbstractImageTypeHandler;
use PrestaShop\PrestaShop\Core\Domain\ImageType\Command\DeleteImageTypeCommand;
use PrestaShop\PrestaShop\Core\Domain\ImageType\CommandHandler\DeleteImageTypeHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\ImageType\Exception\DeleteImageTypeException;
use PrestaShop\PrestaShop\Core\Domain\ImageType\Exception\ImageTypeException;
use PrestaShopException;

/**
 * Handles command that deletes image type
 */
class DeleteImageTypeHandler extends AbstractImageTypeHandler implements DeleteImageTypeHandlerInterface
{
    /**
     * {@inheritdoc}
     */
    public function handle(DeleteImageTypeCommand $command)
    {
        $imageType = $this->getImageType($command->getImageTypeId());

        try {
            if (false === $imageType->delete()) {
                throw new DeleteImageTypeException(sprintf('Cannot delete image type object with id "%d"', $command->getImageTypeId()->getValue()), DeleteImageTypeException::FAILED_DELETE);
            }
        } catch (PrestaShopException $e) {
            throw new ImageTypeException(sprintf('An error occurred when deleting image type with id "%d"', $command->getImageTypeId()->getValue()));
        }
    }
}
