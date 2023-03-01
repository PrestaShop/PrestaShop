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

namespace Tests\Unit\PrestaShopBundle\License;

use PrestaShopBundle\License\AddLicensesLoggerInterface;
use Symfony\Component\Finder\SplFileInfo;

class SpyAddLicensesLogger implements AddLicensesLoggerInterface
{
    /** @var int */
    private $insertCount = 0;

    /** @var int */
    private $dryInsertCount = 0;

    /** @var int */
    private $currentDeletionCount = 0;

    /** @var int */
    private $oldDeletionCount = 0;

    /** @var int */
    private $dryCurrentDeletionCount = 0;

    /** @var int */
    private $dryOldDeletionCount = 0;

    /** @var int */
    private $progressCount = 0;

    /** @var array<string, int> */
    private $startExtensionCount = [];

    /** @var array<string, int> */
    private $finishExtensionCount = [];

    public function logInsertion(SplFileInfo $fileInfo): void
    {
        ++$this->insertCount;
    }

    public function getInsertCount(): int
    {
        return $this->insertCount;
    }

    public function logDryInsertion(SplFileInfo $fileInfo): void
    {
        ++$this->dryInsertCount;
    }

    public function getDryInsertCount(): int
    {
        return $this->dryInsertCount;
    }

    public function logCurrentLicenseDeletions(SplFileInfo $fileInfo): void
    {
        ++$this->currentDeletionCount;
    }

    public function getCurrentDeletionCount(): int
    {
        return $this->currentDeletionCount;
    }

    public function logOldLicenseDeletions(SplFileInfo $fileInfo): void
    {
        ++$this->oldDeletionCount;
    }

    public function getOldDeletionCount(): int
    {
        return $this->oldDeletionCount;
    }

    public function logDryCurrentLicenseDeletions(SplFileInfo $fileInfo): void
    {
        ++$this->dryCurrentDeletionCount;
    }

    public function getDryCurrentDeletionCount(): int
    {
        return $this->dryCurrentDeletionCount;
    }

    public function logDryOldLicenseDeletions(SplFileInfo $fileInfo): void
    {
        ++$this->dryOldDeletionCount;
    }

    public function getDryOldDeletionCount(): int
    {
        return $this->dryOldDeletionCount;
    }

    public function progress(): void
    {
        ++$this->progressCount;
    }

    public function getProgressCount(): int
    {
        return $this->progressCount;
    }

    public function startExtension(string $extension, int $count)
    {
        if (!isset($this->startExtensionCount[$extension])) {
            $this->startExtensionCount[$extension] = 0;
        }
        ++$this->startExtensionCount[$extension];
    }

    public function getStartExtensionCount(string $extension): int
    {
        return $this->startExtensionCount[$extension];
    }

    public function finishExtension(string $extension)
    {
        if (!isset($this->finishExtensionCount[$extension])) {
            $this->finishExtensionCount[$extension] = 0;
        }
        ++$this->finishExtensionCount[$extension];
    }

    public function getFinishExtensionCount(string $extension): int
    {
        return $this->finishExtensionCount[$extension];
    }
}
