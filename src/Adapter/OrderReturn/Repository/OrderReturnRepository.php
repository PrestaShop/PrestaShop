<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\OrderReturn\Repository;

use OrderReturn;
use PrestaShop\PrestaShop\Adapter\OrderReturn\Validator\OrderReturnValidator;
use PrestaShop\PrestaShop\Core\Domain\OrderReturn\Exception\OrderReturnException;
use PrestaShop\PrestaShop\Core\Domain\OrderReturn\Exception\OrderReturnNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\OrderReturn\ValueObject\OrderReturnId;
use PrestaShop\PrestaShop\Core\Exception\CoreException;
use PrestaShop\PrestaShop\Core\Repository\AbstractObjectModelRepository;

class OrderReturnRepository extends AbstractObjectModelRepository
{
    /**
     * @var OrderReturnValidator
     */
    private $orderReturnValidator;

    /**
     * @param OrderReturnValidator $orderReturnValidator
     */
    public function __construct(OrderReturnValidator $orderReturnValidator)
    {
        $this->orderReturnValidator = $orderReturnValidator;
    }

    /**
     * Gets legacy OrderReturn
     *
     * @param OrderReturnId $orderReturnId
     *
     * @return OrderReturn
     *
     * @throws OrderReturnException
     * @throws CoreException
     */
    public function get(OrderReturnId $orderReturnId): OrderReturn
    {
        /** @var OrderReturn $orderReturn */
        $orderReturn = $this->getObjectModel(
            $orderReturnId->getValue(),
            OrderReturn::class,
            OrderReturnNotFoundException::class
        );

        return $orderReturn;
    }

    /**
     * @param OrderReturn $orderReturn
     *
     * @throws CoreException
     */
    public function update(OrderReturn $orderReturn): void
    {
        $this->orderReturnValidator->validate($orderReturn);
        $this->updateObjectModel(
            $orderReturn,
            OrderReturnException::class
        );
    }
}
