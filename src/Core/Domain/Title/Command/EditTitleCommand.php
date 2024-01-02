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

namespace PrestaShop\PrestaShop\Core\Domain\Title\Command;

use PrestaShop\PrestaShop\Core\Domain\Title\Exception\TitleConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Title\ValueObject\Gender;
use PrestaShop\PrestaShop\Core\Domain\Title\ValueObject\TitleId;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Edits title with provided data
 */
class EditTitleCommand
{
    /**
     * @var TitleId
     */
    protected $titleId;

    /**
     * @var array<string>|null
     */
    protected $localizedNames;

    /**
     * @var Gender|null
     */
    protected $gender;

    /**
     * @var UploadedFile|null
     */
    protected $imgFile;

    /**
     * @var int|null
     */
    protected $imgWidth;

    /**
     * @var int|null
     */
    protected $imgHeight;

    /**
     * @param int $titleId
     *
     * @throws TitleConstraintException
     */
    public function __construct(int $titleId)
    {
        $this->titleId = new TitleId($titleId);
    }

    /**
     * @return TitleId
     */
    public function getTitleId(): TitleId
    {
        return $this->titleId;
    }

    /**
     * @return array<string>|null
     */
    public function getLocalizedNames(): ?array
    {
        return $this->localizedNames;
    }

    /**
     * @param array<string> $localizedNames
     *
     * @return self
     */
    public function setLocalizedNames(array $localizedNames): self
    {
        $this->localizedNames = $localizedNames;

        return $this;
    }

    /**
     * @return Gender|null
     */
    public function getGender(): ?Gender
    {
        return $this->gender;
    }

    /**
     * @param int $gender
     *
     * @return self
     */
    public function setGender(int $gender): self
    {
        $this->gender = new Gender($gender);

        return $this;
    }

    /**
     * @return UploadedFile|null
     */
    public function getImageFile(): ?UploadedFile
    {
        return $this->imgFile;
    }

    /**
     * @param UploadedFile $imageFile
     *
     * @return self
     */
    public function setImageFile(UploadedFile $imageFile): self
    {
        $this->imgFile = $imageFile;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getImageWidth(): ?int
    {
        return $this->imgWidth;
    }

    /**
     * @param int|null $imageWidth
     *
     * @return self
     */
    public function setImageWidth(?int $imageWidth): self
    {
        $this->imgWidth = $imageWidth;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getImageHeight(): ?int
    {
        return $this->imgHeight;
    }

    /**
     * @param int|null $imageHeight
     *
     * @return self
     */
    public function setImageHeight(?int $imageHeight): self
    {
        $this->imgHeight = $imageHeight;

        return $this;
    }
}
