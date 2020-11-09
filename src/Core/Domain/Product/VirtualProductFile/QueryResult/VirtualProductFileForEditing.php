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

namespace PrestaShop\PrestaShop\Core\Domain\Product\VirtualProductFile\QueryResult;

use DateTimeInterface;

/**
 * Holds data for editing Virtual Product File
 */
class VirtualProductFileForEditing
{
    /**
     * @var string
     */
    private $filePath;

    /**
     * @var string
     */
    private $displayName;

    /**
     * @var int
     */
    private $accessDays;

    /**
     * @var int
     */
    private $downloadTimesLimit;

    /**
     * @var DateTimeInterface|null
     */
    private $expirationDate;

    /**
     * @param string $filePath
     * @param string $displayName
     * @param int $accessDays
     * @param int $downloadTimesLimit
     * @param DateTimeInterface|null $expirationDate
     */
    public function __construct(
        string $filePath,
        string $displayName,
        int $accessDays,
        int $downloadTimesLimit,
        ?DateTimeInterface $expirationDate
    ) {
        $this->filePath = $filePath;
        $this->displayName = $displayName;
        $this->accessDays = $accessDays;
        $this->downloadTimesLimit = $downloadTimesLimit;
        $this->expirationDate = $expirationDate;
    }

    /**
     * @return string
     */
    public function getFilePath(): string
    {
        return $this->filePath;
    }

    /**
     * @return string
     */
    public function getDisplayName(): string
    {
        return $this->displayName;
    }

    /**
     * @return int
     */
    public function getAccessDays(): int
    {
        return $this->accessDays;
    }

    /**
     * @return int
     */
    public function getDownloadTimesLimit(): int
    {
        return $this->downloadTimesLimit;
    }

    /**
     * @return DateTimeInterface|null
     */
    public function getExpirationDate(): ?DateTimeInterface
    {
        return $this->expirationDate;
    }
}
