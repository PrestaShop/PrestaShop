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

namespace PrestaShop\PrestaShop\Adapter\Product\SpecificPrice\Update;

use PrestaShop\PrestaShop\Adapter\AbstractObjectModelRepository;
use PrestaShop\PrestaShop\Core\Domain\Product\SpecificPrice\Exception\CannotSetSpecificPricePrioritiesException;
use PrestaShop\PrestaShop\Core\Domain\Product\SpecificPrice\ValueObject\PriorityList;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;
use PrestaShop\PrestaShop\Core\Exception\CoreException;
use PrestaShopException;
use SpecificPrice;

/**
 * Responsible for updates related to specific price priorities
 */
class SpecificPricePriorityUpdater extends AbstractObjectModelRepository
{
    /**
     * @param ProductId $productId
     * @param PriorityList $priorityList
     *
     * @throws CannotSetSpecificPricePrioritiesException
     * @throws CoreException
     */
    public function setPrioritiesForProduct(ProductId $productId, PriorityList $priorityList): void
    {
        try {
            if (!SpecificPrice::setSpecificPriority($productId->getValue(), $priorityList->getPriorities())) {
                throw new CannotSetSpecificPricePrioritiesException(sprintf(
                    'Failed to set specific price priorities for product #%d',
                    $productId->getValue()
                ));
            }
        } catch (PrestaShopException $e) {
            throw new CoreException(sprintf(
                'Error occurred when trying to set specific price priorities for product #%d',
                $productId->getValue()
            ));
        }
    }

    /**
     * @param PriorityList $priorityList
     *
     * @throws CannotSetSpecificPricePrioritiesException
     * @throws CoreException
     */
    public function setGlobalPriorities(PriorityList $priorityList): void
    {
        try {
            if (!SpecificPrice::setPriorities($priorityList->getPriorities())) {
                throw new CannotSetSpecificPricePrioritiesException('Failed to set specific price priorities');
            }
        } catch (PrestaShopException $e) {
            throw new CoreException('Error occurred when trying to set specific price priorities');
        }
    }
}
