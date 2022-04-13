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

namespace PrestaShop\PrestaShop\Core\Domain\Carrier\ValueObject;

use PrestaShop\PrestaShop\Core\Domain\Carrier\Exception\CarrierConstraintException;

/**
 * Carriers are referenced by id_reference (instead of usual primary id as most entities)
 */
class CarrierReferenceId
{
    /**
     * @var int
     */
    private $carrierReferenceId;

    /**
     * @param int $carrierReferenceId
     *
     * @throws CarrierConstraintException
     */
    public function __construct($carrierReferenceId)
    {
        $this->assertIntegerIsGreaterThanZero($carrierReferenceId);
        $this->carrierReferenceId = $carrierReferenceId;
    }

    /**
     * @return int
     */
    public function getValue(): int
    {
        return $this->carrierReferenceId;
    }

    /**
     * @param int $carrierReferenceId
     */
    private function assertIntegerIsGreaterThanZero(int $carrierReferenceId)
    {
        if (0 >= $carrierReferenceId) {
            throw new CarrierConstraintException(
                sprintf('CarrierReferenceId "%s" is invalid. It must greater than 0.', $carrierReferenceId),
                CarrierConstraintException::INVALID_REFERENCE_ID
            );
        }
    }
}
