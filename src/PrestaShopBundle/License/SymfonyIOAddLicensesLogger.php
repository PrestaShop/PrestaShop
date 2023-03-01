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

namespace PrestaShopBundle\License;

use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Finder\SplFileInfo;

class SymfonyIOAddLicensesLogger implements AddLicensesLoggerInterface
{
    private $progressBar;

    private $dryInsertMessages = [];

    private $dryCurrentLicenseDeletionMessages = [];

    private $dryOldLicenseDeletionMessages = [];

    private $io;

    public function __construct(SymfonyStyle $io)
    {
        $this->io = $io;
    }

    public function logInsertion(SplFileInfo $fileInfo): void
    {
    }

    public function logDryInsertion(SplFileInfo $fileInfo): void
    {
        $this->dryInsertMessages[] = $fileInfo->getRelativePathname() . ' needs license.';
    }

    public function logCurrentLicenseDeletions(SplFileInfo $fileInfo): void
    {
    }

    public function logOldLicenseDeletions(SplFileInfo $fileInfo): void
    {
    }

    public function logDryCurrentLicenseDeletions(SplFileInfo $fileInfo): void
    {
        $this->dryCurrentLicenseDeletionMessages[] = 'License(s) not at the beginning detected in ' . $fileInfo->getRelativePathname();
    }

    public function logDryOldLicenseDeletions(SplFileInfo $fileInfo): void
    {
        $this->dryOldLicenseDeletionMessages[] = 'Old license(s) detected in ' . $fileInfo->getRelativePathname();
    }

    public function hasDryMessages(): bool
    {
        return count($this->dryInsertMessages) > 0
            || count($this->dryCurrentLicenseDeletionMessages) > 0
            || count($this->dryOldLicenseDeletionMessages) > 0
        ;
    }

    public function logDryMessages(): void
    {
        $dryMessagesCategories = [
            'Missing licenses' => $this->getDryInsertMessages(),
            'Licenses not at the beginning' => $this->getDryCurrentLicenseDeletionsMessages(),
            'Old licenses still present' => $this->getDryOldLicenseDeletionsMessages(),
        ];

        foreach ($dryMessagesCategories as $categoryMessage => $dryMessagesCategory) {
            if (count($dryMessagesCategory) > 0) {
                $this->io->error(
                    $categoryMessage . ":\n" .
                    implode("\n", $dryMessagesCategory)
                );
            }
        }
    }

    /**
     * @return string[]
     */
    public function getDryInsertMessages(): array
    {
        return $this->dryInsertMessages;
    }

    /**
     * @return string[]
     */
    public function getDryCurrentLicenseDeletionsMessages(): array
    {
        return $this->dryCurrentLicenseDeletionMessages;
    }

    /**
     * @return string[]
     */
    public function getDryOldLicenseDeletionsMessages(): array
    {
        return $this->dryOldLicenseDeletionMessages;
    }

    public function progress(): void
    {
        $this->progressBar->advance();
    }

    public function startExtension(string $extension, int $count)
    {
        $this->io->writeln('Updating license in ' . strtoupper($extension) . ' files ...');
        $this->progressBar = $this->io->createProgressBar($count);
        $this->progressBar->setRedrawFrequency(20);
        $this->progressBar->start();
    }

    public function finishExtension(string $extension)
    {
        $this->progressBar->finish();
        $this->io->newLine();
    }
}
