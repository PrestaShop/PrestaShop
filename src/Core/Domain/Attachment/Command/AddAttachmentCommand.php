<?php
/**
 * 2007-2019 PrestaShop and Contributors
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Core\Domain\Attachment\Command;

/**
 * Attachment creation command
 */
class AddAttachmentCommand
{
    /**
     * @var string
     */
    private $pathName;

    /**
     * @var int
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
     * @var string
     */
    private $mimeType;

    /**
     * @var string
     */
    private $originalName;

    /**
     * @param string $pathName
     * @param int $fileSize
     * @param array $localizedNames
     * @param array $localizedDescriptions
     * @param string $mimeType
     * @param string $originalName
     */
    public function __construct(
        string $pathName,
        int $fileSize,
        array $localizedNames,
        array $localizedDescriptions,
        string $mimeType,
        string $originalName
    ) {
        $this->pathName = $pathName;
        $this->fileSize = $fileSize;
        $this->localizedNames = $localizedNames;
        $this->localizedDescriptions = $localizedDescriptions;
        $this->mimeType = $mimeType;
        $this->originalName = $originalName;
    }

    /**
     * @return string
     */
    public function getFilePathName(): string
    {
        return $this->pathName;
    }

    public function getFileSize(): int
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
     * @return string
     */
    public function getMimeType(): string
    {
        return $this->mimeType;
    }

    /**
     * @return string
     */
    public function getOriginalName(): string
    {
        return $this->originalName;
    }
}
