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

namespace PrestaShop\PrestaShop\Core\Domain\MerchandiseReturn\QueryResult;

use PrestaShop\PrestaShop\Core\Domain\Order\QueryResult\OrderProductCustomizationsForViewing;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;

class MerchandiseReturnProductForViewing
{
    /**
     * @var ProductId
     */
    private $productId;

    /**
     * @var int|null
     */
    private $orderDetailId;

    /**
     * @var string
     */
    private $reference;

    /**
     * @var string
     */
    private $name;

    /**
     * @var int
     */
    private $quantity;

    /**
     * @var OrderProductCustomizationsForViewing|null
     */
    private $customizations;

    public function __construct(
        ProductId $productId,
        ?int $orderDetailId,
        string $reference,
        string $name,
        int $quantity,
        ?OrderProductCustomizationsForViewing $customizations = null
    ) {
        $this->productId = $productId;
        $this->orderDetailId = $orderDetailId;
        $this->reference = $reference;
        $this->name = $name;
        $this->quantity = $quantity;
        $this->customizations = $customizations;
    }

    /**
     * @return ProductId
     */
    public function getProductId(): ProductId
    {
        return $this->productId;
    }

    /**
     * @return int|null
     */
    public function getOrderDetailId(): ?int
    {
        return $this->orderDetailId;
    }

    /**
     * @return string
     */
    public function getReference(): string
    {
        return $this->reference;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return int
     */
    public function getQuantity(): int
    {
        return $this->quantity;
    }

    /**
     * @return OrderProductCustomizationsForViewing|null
     */
    public function getCustomizations(): ?OrderProductCustomizationsForViewing
    {
        return $this->customizations;
    }
}
