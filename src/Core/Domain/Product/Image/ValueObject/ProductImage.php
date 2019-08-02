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

namespace PrestaShop\PrestaShop\Core\Domain\Product\Image\ValueObject;

/**
 * Holds product image data.
 */
class ProductImage
{
    /**
     * @var int
     */
    private $position;

    /**
     * @var bool
     */
    private $isCover;

    /**
     * @var array
     */
    private $localizedCaptions;

    /**
     * @param int $position
     * @param bool $isCover
     * @param array $localizedCaptions
     */
    public function __construct(
        int $position,
        bool $isCover,
        array $localizedCaptions
    ){

        $this->position = $position;
        $this->isCover = $isCover;
        $this->localizedCaptions = $localizedCaptions;
    }

    /**
     * @return int
     */
    public function getPosition(): int
    {
        return $this->position;
    }

    /**
     * @return bool
     */
    public function isCover(): bool
    {
        return $this->isCover;
    }

    /**
     * @return array
     */
    public function getLocalizedCaptions(): array
    {
        return $this->localizedCaptions;
    }
}
