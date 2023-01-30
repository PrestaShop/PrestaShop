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

namespace PrestaShop\PrestaShop\Core\Domain\OrderReturn\Command;

use PrestaShop\PrestaShop\Core\Domain\OrderReturn\Exception\OrderReturnConstraintException;
use PrestaShop\PrestaShop\Core\Domain\OrderReturn\ValueObject\OrderReturnDetailId;
use PrestaShop\PrestaShop\Core\Domain\OrderReturn\ValueObject\OrderReturnId;
use PrestaShop\PrestaShop\Core\Domain\Product\Customization\ValueObject\CustomizationId;

/**
 * Deletes product from given order return.
 */
class DeleteProductFromOrderReturnCommand
{
    /**
     * @var OrderReturnId
     */
    private $orderReturnId;

    /**
     * @var OrderReturnDetailId
     */
    private $orderReturnDetailId;
    /**
     * @var CustomizationId|null
     */
    private $customizationId;

    /**
     * DeleteProductFromOrderReturnCommand constructor.
     *
     * @param int $orderReturnId
     * @param int $orderReturnDetailId
     *
     * @throws OrderReturnConstraintException
     */
    public function __construct(int $orderReturnId, int $orderReturnDetailId, ?int $customizationId)
    {
        $this->orderReturnId = new OrderReturnId($orderReturnId);
        $this->orderReturnDetailId = new OrderReturnDetailId($orderReturnDetailId);
        $this->customizationId = $customizationId ? new CustomizationId($customizationId) : null;
    }

    /**
     * @return OrderReturnId
     */
    public function getOrderReturnId(): OrderReturnId
    {
        return $this->orderReturnId;
    }

    /**
     * @return OrderReturnDetailId
     */
    public function getOrderReturnDetailId(): OrderReturnDetailId
    {
        return $this->orderReturnDetailId;
    }

    /**
     * @return CustomizationId|null
     */
    public function getCustomizationId(): ?CustomizationId
    {
        return $this->customizationId;
    }
}
