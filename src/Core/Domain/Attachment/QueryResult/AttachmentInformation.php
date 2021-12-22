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

namespace PrestaShop\PrestaShop\Core\Domain\Attachment\QueryResult;

class AttachmentInformation
{
    /**
     * @var int
     */
    private $attachmentId;

    /**
     * @var array<int, string>
     */
    private $localizedNames;

    /**
     * @var array<int, string>
     */
    private $localizedDescriptions;

    /**
     * @var string
     */
    private $fileName;

    /**
     * @var string
     */
    private $mimeType;

    /**
     * @var int
     */
    private $fileSize;

    /**
     * @param int $attachmentId
     * @param array $localizedNames
     * @param array $localizedDescriptions
     * @param string $fileName
     * @param string $mimeType
     * @param int $fileSize
     */
    public function __construct(
        int $attachmentId,
        array $localizedNames,
        array $localizedDescriptions,
        string $fileName,
        string $mimeType,
        int $fileSize
    ) {
        $this->attachmentId = $attachmentId;
        $this->localizedNames = $localizedNames;
        $this->localizedDescriptions = $localizedDescriptions;
        $this->fileName = $fileName;
        $this->mimeType = $mimeType;
        $this->fileSize = $fileSize;
    }

    /**
     * @return int
     */
    public function getAttachmentId(): int
    {
        return $this->attachmentId;
    }

    /**
     * @return string[]
     */
    public function getLocalizedNames(): array
    {
        return $this->localizedNames;
    }

    /**
     * @return string[]
     */
    public function getLocalizedDescriptions(): array
    {
        return $this->localizedDescriptions;
    }

    /**
     * @return string
     */
    public function getFileName(): string
    {
        return $this->fileName;
    }

    /**
     * @return string
     */
    public function getMimeType(): string
    {
        return $this->mimeType;
    }

    /**
     * @return int
     */
    public function getFileSize(): int
    {
        return $this->fileSize;
    }
}
