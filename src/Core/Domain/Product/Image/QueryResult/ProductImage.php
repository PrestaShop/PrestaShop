<?php
/**
 * 2007-2020 PrestaShop SA and Contributors
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
 * @copyright 2007-2020 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Product\Image\QueryResult;

class ProductImage
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var int
     */
    private $productId;

    /**
     * @var string
     */
    private $basePath;

    /**
     * @var string[]
     */
    private $localizedLegends;

    /**
     * @var int
     */
    private $position;

    /**
     * @var bool
     */
    private $cover;

    /**
     * @param int $id
     * @param int $productId
     * @param string $basePath
     * @param array $localizedLegends
     * @param int $position
     * @param bool $cover
     */
    public function __construct(
        int $id,
        int $productId,
        string $basePath,
        array $localizedLegends,
        int $position,
        bool $cover
    ) {
        $this->id = $id;
        $this->productId = $productId;
        $this->basePath = $basePath;
        $this->localizedLegends = $localizedLegends;
        $this->position = $position;
        $this->cover = $cover;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return int
     */
    public function getProductId(): int
    {
        return $this->productId;
    }

    /**
     * @return string
     */
    public function getBasePath(): string
    {
        return $this->basePath;
    }

    /**
     * @return array
     */
    public function getLocalizedLegends(): array
    {
        return $this->localizedLegends;
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
        return $this->cover;
    }
}
