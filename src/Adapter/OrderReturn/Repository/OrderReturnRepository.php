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

namespace PrestaShop\PrestaShop\Adapter\OrderReturn\Repository;

use OrderReturn;
use PrestaShop\PrestaShop\Adapter\AbstractObjectModelRepository;
use PrestaShop\PrestaShop\Adapter\OrderReturn\Validator\OrderReturnValidator;
use PrestaShop\PrestaShop\Core\Domain\OrderReturn\Exception\OrderReturnException;
use PrestaShop\PrestaShop\Core\Domain\OrderReturn\Exception\OrderReturnNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\OrderReturn\ValueObject\OrderReturnId;
use PrestaShop\PrestaShop\Core\Exception\CoreException;

class OrderReturnRepository extends AbstractObjectModelRepository
{
    /**
     * @var OrderReturnValidator
     */
    private $validator;

    public function __construct(OrderReturnValidator $validator)
    {
        $this->validator = $validator;
    }

    /**
     * Gets legacy OrderReturn
     *
     * @param OrderReturnId $orderReturnId
     *
     * @return OrderReturn
     *
     * @throws OrderReturnException
     */
    public function get(OrderReturnId $orderReturnId): OrderReturn
    {
        /** @var OrderReturn $orderReturn */
        return $this->getObjectModel($orderReturnId->getValue(), OrderReturn::class, OrderReturnNotFoundException::class);
    }

    /**
     * @param OrderReturn $orderReturn
     *
     * @throws CoreException
     */
    public function update(OrderReturn $orderReturn): void
    {
        $this->validator->validate($orderReturn);
        $this->updateObjectModel(
            $orderReturn,
            OrderReturnException::class
        );
    }
}
