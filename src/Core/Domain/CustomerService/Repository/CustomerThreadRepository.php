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

namespace PrestaShop\PrestaShop\Core\Domain\CustomerService\Repository;

use CustomerThread;
use PrestaShop\PrestaShop\Core\Domain\CustomerService\Exception\CannotDeleteCustomerThreadException;
use PrestaShop\PrestaShop\Core\Domain\CustomerService\Exception\CustomerThreadNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\CustomerService\ValueObject\CustomerThreadId;
use PrestaShop\PrestaShop\Core\Repository\AbstractObjectModelRepository;

/**
 * Methods to access data storage for customerThread
 */
class CustomerThreadRepository extends AbstractObjectModelRepository
{
    /**
     * @throws CustomerThreadNotFoundException
     */
    public function get(CustomerThreadId $customerThreadId): CustomerThread
    {
        /** @var CustomerThread $customerThread */
        $customerThread = $this->getObjectModel(
            $customerThreadId->getValue(),
            CustomerThread::class,
            CustomerThreadNotFoundException::class
        );

        return $customerThread;
    }

    /**
     * @throws CannotDeleteCustomerThreadException
     */
    public function delete(CustomerThreadId $customerThreadId): void
    {
        $this->deleteObjectModel($this->get($customerThreadId), CannotDeleteCustomerThreadException::class);
    }
}
