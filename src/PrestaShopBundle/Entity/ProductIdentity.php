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

namespace PrestaShopBundle\Entity;

use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class ProductIdentity
{
    /**
     * @var int
     */
    private int $productId;

    /**
     * @var int
     */
    private int $combinationId = 0;

    public function __construct(int $productId, int $combinationId = 0)
    {
        $this->productId = $productId;
        $this->combinationId = $combinationId;
    }

    public static function fromArray(array $identifiers): ProductIdentity
    {
        if (!array_key_exists('product_id', $identifiers)) {
            throw new BadRequestHttpException('The "productId" parameter is required');
        }

        $productId = (int) $identifiers['product_id'];

        $combinationId = 0;
        if (array_key_exists('combination_id', $identifiers)) {
            $combinationId = (int) $identifiers['combination_id'];
        }

        return new static($productId, $combinationId);
    }

    public function getProductId(): int
    {
        return $this->productId;
    }

    public function getCombinationId(): int
    {
        return $this->combinationId;
    }
}
