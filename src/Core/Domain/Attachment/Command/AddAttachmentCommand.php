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

namespace PrestaShop\PrestaShop\Core\Domain\Attachment\Command;

/**
 * Attachment creation command
 */
class AddAttachmentCommand
{
    /**
     * @var string|null
     */
    private $pathName;

    /**
     * @var int|null
     */
    private $fileSize;

    /**
     * @var string[]
     */
    private $localizedNames;

    /**
     * @var string[]
     */
    private $localizedDescriptions = [];

    /**
     * @var string|null
     */
    private $mimeType;

    /**
     * @var string|null
     */
    private $originalName;

    /**
     * @param array $localizedNames
     * @param array $localizedDescriptions
     */
    public function __construct(
        array $localizedNames,
        array $localizedDescriptions
    ) {
        $this->localizedNames = $localizedNames;
        $this->localizedDescriptions = $localizedDescriptions;
    }

    /**
     * @param string $pathName
     * @param int $fileSize
     * @param string $mimeType
     * @param string $originalName
     */
    public function setFileInformation(
        string $pathName,
        int $fileSize,
        string $mimeType,
        string $originalName
    ): void {
        $this->pathName = $pathName;
        $this->fileSize = $fileSize;
        $this->mimeType = $mimeType;
        $this->originalName = $originalName;
    }

    /**
     * @return string|null
     */
    public function getFilePathName(): ?string
    {
        return $this->pathName;
    }

    /**
     * @return int|null
     */
    public function getFileSize(): ?int
    {
        return $this->fileSize;
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
     * @return string|null
     */
    public function getMimeType(): ?string
    {
        return $this->mimeType;
    }

    /**
     * @return string|null
     */
    public function getOriginalName(): ?string
    {
        return $this->originalName;
    }
}
