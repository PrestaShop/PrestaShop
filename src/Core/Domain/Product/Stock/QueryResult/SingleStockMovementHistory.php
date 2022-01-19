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

namespace PrestaShop\PrestaShop\Core\Domain\Product\Stock\QueryResult;

use PrestaShop\PrestaShop\Core\Util\DateTime\DateTime;
use Symfony\Contracts\Translation\TranslatorInterface;

class SingleStockMovementHistory implements StockMovementHistory
{
    /**
     * @var StockMovement
     */
    protected $stockMovement;

    public function __construct(StockMovement $stockMovement)
    {
        $this->stockMovement = $stockMovement;
    }

    public function getStockMovement(): StockMovement
    {
        return $this->stockMovement;
    }

    public function getStockMovementIds(): array
    {
        return [$this->getStockMovement()->getStockMovementId()];
    }

    public function getStockIds(): array
    {
        return [$this->getStockMovement()->getStockId()];
    }

    public function getStockMovementReasonIds(): array
    {
        return [$this->getStockMovement()->getStockMovementReasonId()];
    }

    public function getOrderIds(): array
    {
        return [$this->getStockMovement()->getOrderId()];
    }

    public function getEmployeeIds(): array
    {
        return [$this->getStockMovement()->getEmployeeId()];
    }

    public function getEmployeeName(?TranslatorInterface $translator = null): ?string
    {
        $nameParts = [
            'firstname' => $this->getStockMovement()->getEmployeeFirstName(),
            'lastname' => $this->getStockMovement()->getEmployeeLastName(),
        ];
        if ($translator instanceof TranslatorInterface) {
            // TODO Fix translation key & domain
            $name = $translator->trans('%firstname% %lastname%', $nameParts, 'domain???');
        } else {
            $name = implode(' ', array_filter($nameParts));
        }

        return $name;
    }

    public function getDeltaQuantity(): int
    {
        return $this->getStockMovement()->getDeltaQuantity();
    }

    public function getDates(): array
    {
        return [
            'add' => $this->getStockMovement()->getDateAdd(),
        ];
    }

    public function getDateRange(?TranslatorInterface $translator = null): string
    {
        return $this
            ->getStockMovement()
            ->getDateAdd()
            ->format(DateTime::DEFAULT_DATETIME_FORMAT)
            ;
    }
}
