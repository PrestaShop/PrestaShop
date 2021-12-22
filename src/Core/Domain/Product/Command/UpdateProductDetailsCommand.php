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

namespace PrestaShop\PrestaShop\Core\Domain\Product\Command;

use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\Ean13;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\Isbn;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\Reference;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\Upc;

/**
 * Updates product details
 */
class UpdateProductDetailsCommand
{
    /**
     * @var ProductId
     */
    private $productId;

    /**
     * @var Isbn|null
     */
    private $isbn;

    /**
     * @var Upc|null
     */
    private $upc;

    /**
     * @var Ean13|null
     */
    private $ean13;

    /**
     * @var string|null
     */
    private $mpn;

    /**
     * @var Reference|null
     */
    private $reference;

    /**
     * @param int $productId
     */
    public function __construct(int $productId)
    {
        $this->productId = new ProductId($productId);
    }

    /**
     * @return ProductId
     */
    public function getProductId(): ProductId
    {
        return $this->productId;
    }

    /**
     * @return Isbn|null
     */
    public function getIsbn(): ?Isbn
    {
        return $this->isbn;
    }

    /**
     * @param string $isbn
     *
     * @return UpdateProductDetailsCommand
     */
    public function setIsbn(string $isbn): UpdateProductDetailsCommand
    {
        $this->isbn = new Isbn($isbn);

        return $this;
    }

    /**
     * @return Upc|null
     */
    public function getUpc(): ?Upc
    {
        return $this->upc;
    }

    /**
     * @param string $upc
     *
     * @return UpdateProductDetailsCommand
     */
    public function setUpc(string $upc): UpdateProductDetailsCommand
    {
        $this->upc = new Upc($upc);

        return $this;
    }

    /**
     * @return Ean13|null
     */
    public function getEan13(): ?Ean13
    {
        return $this->ean13;
    }

    /**
     * @param string $ean13
     *
     * @return UpdateProductDetailsCommand
     */
    public function setEan13(string $ean13): UpdateProductDetailsCommand
    {
        $this->ean13 = new Ean13($ean13);

        return $this;
    }

    /**
     * @return string|null
     */
    public function getMpn(): ?string
    {
        return $this->mpn;
    }

    /**
     * @param string $mpn
     *
     * @return UpdateProductDetailsCommand
     */
    public function setMpn(string $mpn): UpdateProductDetailsCommand
    {
        $this->mpn = $mpn;

        return $this;
    }

    /**
     * @return Reference|null
     */
    public function getReference(): ?Reference
    {
        return $this->reference;
    }

    /**
     * @param string $reference
     *
     * @return UpdateProductDetailsCommand
     */
    public function setReference(string $reference): UpdateProductDetailsCommand
    {
        $this->reference = new Reference($reference);

        return $this;
    }
}
