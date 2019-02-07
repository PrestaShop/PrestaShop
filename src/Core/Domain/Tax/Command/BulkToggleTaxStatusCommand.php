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

namespace PrestaShop\PrestaShop\Core\Domain\Tax\Command;

use PrestaShop\PrestaShop\Core\Domain\Tax\ValueObject\TaxId;
use PrestaShop\PrestaShop\Core\Domain\Tax\ValueObject\TaxStatus;

/**
 * Toggles taxes status on bulk action
 */
class BulkToggleTaxStatusCommand
{
    /**
     * @var TaxStatus
     */
    private $status;

    /**
     * @var TaxId[]
     */
    private $taxIds;

    /**
     * @param int[] $taxIds
     * @param TaxStatus $status
     *
     * @throws \PrestaShop\PrestaShop\Core\Domain\Tax\Exception\TaxException
     */
    public function __construct(array $taxIds, TaxStatus $status)
    {
        $this->status = $status;
        $this->setTaxIds($taxIds);
    }

    /**
     * @return TaxStatus
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @return TaxId[]
     */
    public function getTaxIds()
    {
        return $this->taxIds;
    }

    /**
     * @param array $taxIds
     *
     * @throws \PrestaShop\PrestaShop\Core\Domain\Tax\Exception\TaxException
     */
    private function setTaxIds(array $taxIds)
    {
        foreach ($taxIds as $taxId) {
            $this->taxIds[] = new TaxId($taxId);
        }
    }
}
