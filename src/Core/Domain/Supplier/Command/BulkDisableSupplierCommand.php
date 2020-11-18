<?php
/**
 * 2007-2019 PrestaShop and Contributors
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
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Core\Domain\Supplier\Command;

use PrestaShop\PrestaShop\Core\Domain\Supplier\Exception\SupplierConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Supplier\Exception\SupplierException;
use PrestaShop\PrestaShop\Core\Domain\Supplier\ValueObject\SupplierId;

/**
 * Class BulkDisableSupplierCommand is responsible for disabling multiple suppliers.
 */
class BulkDisableSupplierCommand extends AbstractBulkSupplierCommand
{
    /**
     * @var SupplierId[]
     */
    private $supplierIds;

    /**
     * @param int[] $supplierIds
     *
     * @throws SupplierException
     * @throws SupplierConstraintException
     */
    public function __construct(array $supplierIds)
    {
        if ($this->assertIsEmptyOrContainsNonIntegerValues($supplierIds)) {
            throw new SupplierConstraintException(
                sprintf(
                    'Missing supplier data or array %s contains non integer values for bulk disable',
                    var_export($supplierIds, true)
                ),
                SupplierConstraintException::INVALID_BULK_DATA
            );
        }

        $this->setSupplierIds($supplierIds);
    }

    /**
     * @return SupplierId[]
     */
    public function getSupplierIds()
    {
        return $this->supplierIds;
    }

    /**
     * @param array $supplierIds
     *
     * @throws SupplierException
     */
    private function setSupplierIds(array $supplierIds)
    {
        foreach ($supplierIds as $id) {
            $this->supplierIds[] = new SupplierId($id);
        }
    }
}
