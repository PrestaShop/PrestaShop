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
use PrestaShop\PrestaShop\Core\Domain\MerchandiseReturn\ValueObject\CustomizationId;
use PrestaShop\PrestaShop\Core\Domain\MerchandiseReturn\ValueObject\MerchandiseReturnDetailId;
use PrestaShop\PrestaShop\Core\Domain\MerchandiseReturn\ValueObject\MerchandiseReturnId;

/**
 * Deletes product from given order.
 */
class DeleteProductFromMerchandiseReturnCommand
{
    /**
     * @var MerchandiseReturnId
     */
    private $merchandiseReturnId;

    /**
     * @var MerchandiseReturnDetailId
     */
    private $merchandiseReturnDetailId;

    /**
     * @var CustomizationId|null
     */
    private $customizationId;

    /**
     * DeleteProductFromMerchandiseReturnCommand constructor.
     *
     * @param int $merchandiseReturnId
     * @param int $merchandiseReturnDetailId
     * @param int|null $customizationId
     *
     * @throws MerchandiseReturnConstraintException
     */
    public function __construct(int $merchandiseReturnId, int $merchandiseReturnDetailId, ?int $customizationId = null)
    {
        $this->merchandiseReturnId = new MerchandiseReturnId($merchandiseReturnId);
        $this->merchandiseReturnDetailId = new MerchandiseReturnDetailId($merchandiseReturnDetailId);

        /**
         * @param null|int $customizationId must remain null if customizationId is 0
         */
        if ($customizationId !== 0 && $customizationId !== null) {
            $this->customizationId = new CustomizationId($customizationId);
        }
    }

    /**
     * @return MerchandiseReturnId
     */
    public function getMerchandiseReturnId(): MerchandiseReturnId
    {
        return $this->merchandiseReturnId;
    }

    /**
     * @return MerchandiseReturnDetailId
     */
    public function getMerchandiseReturnDetailId(): MerchandiseReturnDetailId
    {
        return $this->merchandiseReturnDetailId;
    }

    /**
     * @return CustomizationId|null
     */
    public function getCustomizationId(): ?CustomizationId
    {
        return $this->customizationId;
    }
}
