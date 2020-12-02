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

namespace PrestaShop\PrestaShop\Core\Domain\Product\Combination\Command;

use PrestaShop\PrestaShop\Core\Domain\Product\Combination\ValueObject\CombinationId;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\ProductConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\Ean13;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\Isbn;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\Reference;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\Upc;

/**
 * Updates combination options
 */
class UpdateCombinationOptionsCommand
{
    /**
     * @var CombinationId
     */
    private $combinationId;

    /**
     * @var Ean13|null
     */
    private $ean13;

    /**
     * @var Isbn|null
     */
    private $isbn;

    /**
     * @var string|null
     */
    private $mpn;

    /**
     * @var Reference|null
     */
    private $reference;

    /**
     * @var Upc|null
     */
    private $upc;

    /**
     * @param int $combinationId
     *
     * @throws ProductConstraintException
     */
    public function __construct(
        int $combinationId
    ) {
        $this->combinationId = new CombinationId($combinationId);
    }

    /**
     * @return CombinationId
     */
    public function getCombinationId(): CombinationId
    {
        return $this->combinationId;
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
     * @return UpdateCombinationOptionsCommand
     */
    public function setEan13(string $ean13): UpdateCombinationOptionsCommand
    {
        $this->ean13 = new Ean13($ean13);

        return $this;
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
     * @return UpdateCombinationOptionsCommand
     */
    public function setIsbn(string $isbn): UpdateCombinationOptionsCommand
    {
        $this->isbn = new Isbn($isbn);

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
     * @return UpdateCombinationOptionsCommand
     */
    public function setMpn(string $mpn): UpdateCombinationOptionsCommand
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
     * @return UpdateCombinationOptionsCommand
     */
    public function setReference(string $reference): UpdateCombinationOptionsCommand
    {
        $this->reference = new Reference($reference);

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
     * @return UpdateCombinationOptionsCommand
     */
    public function setUpc(string $upc): UpdateCombinationOptionsCommand
    {
        $this->upc = new Upc($upc);

        return $this;
    }
}
