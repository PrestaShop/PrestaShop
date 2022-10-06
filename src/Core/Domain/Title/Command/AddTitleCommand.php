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

/**
 * Creates title with provided data
 */
class AddTitleCommand
{
    /**
     * @var array<string>
     */
    protected $localizedNames;

    /**
     * @var int
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
     * @param array<string> $localizedNames
     * @param int $genderType
     * @param string|null $imgPathname
     * @param int|null $imgWidth
     * @param int|null $imgHeight
     */
    public function __construct(
        array $localizedNames,
        int $genderType,
        ?string $imgPathname,
        ?int $imgWidth,
        ?int $imgHeight
    ) {
        $this->localizedNames = $localizedNames;
        $this->genderType = $genderType;
        $this->imgPathname = $imgPathname;
        $this->imgWidth = $imgWidth;
        $this->imgHeight = $imgHeight;
    }

    /**
     * @return array<string>
     */
    public function getLocalizedNames(): array
    {
        return $this->localizedNames;
    }

    /**
     * @return int
     */
    public function getGenderType(): int
    {
        return $this->genderType;
    }

    /**
     * @return string|null
     */
    public function getImagePathname(): ?string
    {
        return $this->imgPathname;
    }

    /**
     * @return int|null
     */
    public function getImageWidth(): ?int
    {
        return $this->imgWidth;
    }

    /**
     * @return int|null
     */
    public function getImageHeight(): ?int
    {
        return $this->imgHeight;
    }
}
