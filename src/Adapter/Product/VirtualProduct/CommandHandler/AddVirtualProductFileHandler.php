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

namespace PrestaShop\PrestaShop\Adapter\Product\VirtualProduct\CommandHandler;

use PrestaShop\PrestaShop\Adapter\Product\VirtualProduct\Update\VirtualProductUpdater;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\Product\VirtualProductFile\Command\AddVirtualProductFileCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\VirtualProductFile\CommandHandler\AddVirtualProductFileHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\VirtualProductFile\ValueObject\VirtualProductFileId;
use PrestaShop\PrestaShop\Core\Util\DateTime\DateTime;
use ProductDownload as VirtualProductFile;

/**
 * Handles @see AddVirtualProductFileCommand using legacy object model
 *
 * Legacy object ProductDownload is referred as VirtualProductFile in Core
 */
#[AsCommandHandler]
final class AddVirtualProductFileHandler implements AddVirtualProductFileHandlerInterface
{
    /**
     * @var VirtualProductUpdater
     */
    private $virtualProductUpdater;

    /**
     * @param VirtualProductUpdater $virtualProductUpdater
     */
    public function __construct(
        VirtualProductUpdater $virtualProductUpdater
    ) {
        $this->virtualProductUpdater = $virtualProductUpdater;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(AddVirtualProductFileCommand $command): VirtualProductFileId
    {
        return $this->virtualProductUpdater->addFile(
            $command->getProductId(),
            $command->getFilePath(),
            $this->buildObjectModel($command)
        );
    }

    /**
     * @param AddVirtualProductFileCommand $command
     *
     * @return VirtualProductFile
     */
    private function buildObjectModel(AddVirtualProductFileCommand $command): VirtualProductFile
    {
        $virtualProductFile = new VirtualProductFile();
        $virtualProductFile->display_filename = $command->getDisplayName();
        $virtualProductFile->nb_days_accessible = $command->getAccessDays() ?: 0;
        $virtualProductFile->nb_downloadable = $command->getDownloadTimesLimit() ?: 0;
        $virtualProductFile->date_expiration = $command->getExpirationDate() ?
            $command->getExpirationDate()->format(DateTime::DEFAULT_DATETIME_FORMAT) :
            null
        ;

        return $virtualProductFile;
    }
}
