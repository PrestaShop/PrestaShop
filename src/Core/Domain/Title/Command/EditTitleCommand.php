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
use PrestaShop\PrestaShop\Core\Domain\Title\ValueObject\TitleId;

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
     * @var int|null
     */
    protected $genderType;

    /**
     * @var string|null
     */
    protected $imgPathname;

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
     * @return int|null
     */
    public function getGenderType(): ?int
    {
        return $this->genderType;
    }

    /**
     * @param int $genderType
     *
     * @return self
     */
    public function setGenderType(int $genderType): self
    {
        $this->genderType = $genderType;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getImagePathname(): ?string
    {
        return $this->imgPathname;
    }

    /**
     * @param string $imagePathname
     *
     * @return self
     */
    public function setImagePathname(string $imagePathname): self
    {
        $this->imgPathname = $imagePathname;

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
     * @param int $imageWidth
     *
     * @return self
     */
    public function setImageWidth(int $imageWidth): self
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
     * @param int $imageHeight
     *
     * @return self
     */
    public function setImageHeight(int $imageHeight): self
    {
        $this->imgHeight = $imageHeight;

        return $this;
    }
}
