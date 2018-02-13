<?php
/**
 * 2007-2018 PrestaShop
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
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShopBundle\Entity;

use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class ProductIdentity
{
    private $productId;

    private $combinationId = 0;

    /**
     * @param $productId
     * @param int $combinationId
     */
    public function __construct($productId, $combinationId = 0)
    {
        $this->productId = $productId;
        $this->combinationId = $combinationId;
    }

    /**
     * @param array $identifiers
     * @return ProductIdentity
     */
    public static function fromArray(array $identifiers)
    {
        if (!array_key_exists('product_id', $identifiers)) {
            throw new BadRequestHttpException('The "productId" parameter is required');
        }

        $productId = (int)$identifiers['product_id'];

        $combinationId = 0;
        if (array_key_exists('combination_id', $identifiers)) {
            $combinationId = (int)$identifiers['combination_id'];
        }

        return new self($productId, $combinationId);
    }

    /**
     * @return mixed
     */
    public function getProductId()
    {
        return $this->productId;
    }

    /**
     * @return int
     */
    public function getCombinationId()
    {
        return $this->combinationId;
    }
}
