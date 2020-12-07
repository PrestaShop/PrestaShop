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

namespace PrestaShop\PrestaShop\Adapter\SpecificPrice\Repository;

use PrestaShop\PrestaShop\Adapter\AbstractObjectModelRepository;
use PrestaShop\PrestaShop\Adapter\SpecificPrice\Validate\SpecificPriceValidator;
use PrestaShop\PrestaShop\Core\Domain\SpecificPrice\Exception\CannotAddSpecificPriceException;
use PrestaShop\PrestaShop\Core\Domain\SpecificPrice\Exception\SpecificPriceConstraintException;
use PrestaShop\PrestaShop\Core\Domain\SpecificPrice\ValueObject\SpecificPriceId;
use PrestaShop\PrestaShop\Core\Exception\CoreException;
use SpecificPrice;

/**
 * Methods to access data storage for SpecificPrice
 */
class SpecificPriceRepository extends AbstractObjectModelRepository
{
    /**
     * @var SpecificPriceValidator
     */
    private $specificPriceValidator;

    /**
     * @param SpecificPriceValidator $specificPriceValidator
     */
    public function __construct(
        SpecificPriceValidator $specificPriceValidator
    ) {
        $this->specificPriceValidator = $specificPriceValidator;
    }

    /**
     * @param SpecificPrice $specificPrice
     * @param int $errorCode
     *
     * @return SpecificPriceId
     *
     * @throws SpecificPriceConstraintException
     * @throws CoreException
     */
    public function add(SpecificPrice $specificPrice, int $errorCode = 0): SpecificPriceId
    {
        $this->specificPriceValidator->validate($specificPrice);
        $id = $this->addObjectModel($specificPrice, CannotAddSpecificPriceException::class, $errorCode);

        return new SpecificPriceId($id);
    }
}
