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

namespace PrestaShop\PrestaShop\Core\Domain\MerchandiseReturn\Command;

use PrestaShop\PrestaShop\Core\Domain\MerchandiseReturn\Exception\MerchandiseReturnConstraintException;
use PrestaShop\PrestaShop\Core\Domain\MerchandiseReturn\Exception\MerchandiseReturnException;
use PrestaShop\PrestaShop\Core\Domain\MerchandiseReturn\ValueObject\MerchandiseReturnProduct;
use PrestaShop\PrestaShop\Core\Domain\MerchandiseReturn\ValueObject\MerchandiseReturnId;

/**
 * Deletes products from given merchandise return.
 */
class BulkDeleteProductFromMerchandiseReturnCommand
{
    /**
     * @var MerchandiseReturnId
     */
    private $merchandiseReturnId;

    /**
     * @var MerchandiseReturnProduct[]
     */
    private $merchandiseReturnProducts;

    /**
     * @param int $merchandiseReturnId
     * @param MerchandiseReturnProduct[] $merchandiseReturnProducts
     *
     * @throws MerchandiseReturnException
     * @throws MerchandiseReturnConstraintException
     */
    public function __construct(
        int $merchandiseReturnId,
        array $merchandiseReturnProducts
    ) {
        $this->merchandiseReturnId = new MerchandiseReturnId($merchandiseReturnId);
        $this->setMerchandiseReturnProducts($merchandiseReturnProducts);
    }

    /**
     * @param MerchandiseReturnProduct[] $merchandiseReturnProducts
     *
     * @throws MerchandiseReturnException
     */
    private function setMerchandiseReturnProducts(array $merchandiseReturnProducts): void
    {
        foreach ($merchandiseReturnProducts as $merchandiseReturnProduct) {
            if (!$merchandiseReturnProduct instanceof MerchandiseReturnProduct) {
                throw new MerchandiseReturnConstraintException(
                    'merchandise return details array must instances of MerchandiseReturnProduct'
                );
            }
            $this->merchandiseReturnProducts[] = $merchandiseReturnProduct;
        }
    }

    /**
     * @return MerchandiseReturnProduct[]
     */
    public function getMerchandiseReturnProducts(): array
    {
        return $this->merchandiseReturnProducts;
    }

    /**
     * @return MerchandiseReturnId
     */
    public function getMerchandiseReturnId(): MerchandiseReturnId
    {
        return $this->merchandiseReturnId;
    }
}
