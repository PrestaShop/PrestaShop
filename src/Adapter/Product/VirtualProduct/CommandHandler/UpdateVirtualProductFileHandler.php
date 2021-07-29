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

use PrestaShop\PrestaShop\Adapter\Product\VirtualProduct\Repository\VirtualProductFileRepository;
use PrestaShop\PrestaShop\Adapter\Product\VirtualProduct\Update\VirtualProductUpdater;
use PrestaShop\PrestaShop\Core\Domain\Product\VirtualProductFile\Command\UpdateVirtualProductFileCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\VirtualProductFile\CommandHandler\UpdateVirtualProductFileHandlerInterface;
use PrestaShop\PrestaShop\Core\Util\DateTime\DateTime;
use ProductDownload as VirtualProductFile;

/**
 * Updates VirtualProductFile using legacy object model. (ProductDownload is referenced as VirtualProduct in core)
 */
final class UpdateVirtualProductFileHandler implements UpdateVirtualProductFileHandlerInterface
{
    /**
     * @var VirtualProductUpdater
     */
    private $virtualProductUpdater;

    /**
     * @var VirtualProductFileRepository
     */
    private $virtualProductFileRepository;

    /**
     * @param VirtualProductUpdater $virtualProductUpdater
     * @param VirtualProductFileRepository $virtualProductFileRepository
     */
    public function __construct(
        VirtualProductUpdater $virtualProductUpdater,
        VirtualProductFileRepository $virtualProductFileRepository
    ) {
        $this->virtualProductUpdater = $virtualProductUpdater;
        $this->virtualProductFileRepository = $virtualProductFileRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(UpdateVirtualProductFileCommand $command): void
    {
        $virtualProductFile = $this->virtualProductFileRepository->get($command->getVirtualProductFileId());
        $this->fillEntityWithCommandData($virtualProductFile, $command);

        $this->virtualProductUpdater->updateFile(
            $virtualProductFile,
            $command->getFilePath()
        );
    }

    /**
     * @param VirtualProductFile $virtualProductFile
     * @param UpdateVirtualProductFileCommand $command
     */
    private function fillEntityWithCommandData(VirtualProductFile $virtualProductFile, UpdateVirtualProductFileCommand $command): void
    {
        if (null !== $command->getDisplayName()) {
            $virtualProductFile->display_filename = $command->getDisplayName();
        }
        if (null !== $command->getAccessDays()) {
            $virtualProductFile->nb_days_accessible = $command->getAccessDays();
        }
        if (null !== $command->getDownloadTimesLimit()) {
            $virtualProductFile->nb_downloadable = $command->getDownloadTimesLimit();
        }
        if (null !== $command->getExpirationDate()) {
            $virtualProductFile->date_expiration = $command->getExpirationDate()->format(DateTime::DEFAULT_DATE_FORMAT);
        }
    }
}
