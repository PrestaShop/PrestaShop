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

use PrestaShop\PrestaShop\Core\Domain\Attachment\Exception\AttachmentConstraintException;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Class CreateAttachmentCommand
 */
class CreateAttachmentCommand
{
    /**
     * @var UploadedFile|null
     */
    private $file;

    /**
     * @var string
     */
    private $uniqueFileName;

    /**
     * @var string[]
     */
    private $localizedNames;

    /**
     * @var string[]|null
     */
    private $localizedDescriptions;

    /**
     * @var string
     */
    private $mimeType;

    /**
     * @return UploadedFile|null
     */
    public function getFile(): ?UploadedFile
    {
        return $this->file;
    }

    /**
     * @param UploadedFile|null $file
     *
     * @return CreateAttachmentCommand
     */
    public function setFile(?UploadedFile $file): CreateAttachmentCommand
    {
        $this->file = $file;

        return $this;
    }

    /**
     * @return string
     */
    public function getUniqueFileName(): string
    {
        return $this->uniqueFileName;
    }

    /**
     * @param string $uniqueFileName
     *
     * @return CreateAttachmentCommand
     */
    public function setUniqueFileName(string $uniqueFileName): CreateAttachmentCommand
    {
        $this->uniqueFileName = $uniqueFileName;

        return $this;
    }

    /**
     * @return string[]
     */
    public function getLocalizedNames(): array
    {
        return $this->localizedNames;
    }

    /**
     * @param array $localizedNames
     *
     * @return CreateAttachmentCommand
     *
     * @throws AttachmentConstraintException
     */
    public function setLocalizedNames(array $localizedNames): CreateAttachmentCommand
    {
        if (empty($localizedNames)) {
            throw new AttachmentConstraintException(
                'Attachment name cannot be empty',
                AttachmentConstraintException::EMPTY_NAME
            );
        }

        $this->localizedNames = $localizedNames;

        return $this;
    }

    /**
     * @return string[]|null
     */
    public function getLocalizedDescriptions(): ?array
    {
        return $this->localizedDescriptions;
    }

    /**
     * @param string[]|null $localizedDescriptions
     *
     * @return CreateAttachmentCommand
     */
    public function setLocalizedDescriptions(?array $localizedDescriptions): CreateAttachmentCommand
    {
        $this->localizedDescriptions = $localizedDescriptions;

        return $this;
    }

    /**
     * @return string
     */
    public function getMimeType(): string
    {
        return $this->mimeType;
    }

    /**
     * @param string $mimeType
     *
     * @return CreateAttachmentCommand
     */
    public function setMimeType(string $mimeType): CreateAttachmentCommand
    {
        $this->mimeType = $mimeType;

        return $this;
    }
}
