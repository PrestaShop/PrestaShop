<?php
/**
 * 2007-2015 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 *  @author 	PrestaShop SA <contact@prestashop.com>
 *  @copyright  2007-2015 PrestaShop SA
 *  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */
namespace PrestaShop\PrestaShop\Adapter\Product;

use PrestaShop\PrestaShop\Core\Foundation\Exception\WarningException;
use PrestaShop\PrestaShop\Core\Foundation\Exception\ErrorException;

/**
 * This class will update/insert/delete data from DB / ORM about Product, for both Front and Admin interfaces.
 */
class ProductDataUpdater
{
    /**
     * Activate or deactivate a list of products.
     *
     * @param array $productListId The ID list of products to (de)activate
     * @param boolean $activate True to activate, false to deactivate.
     * @throws WarningException If an error occured during update (not blocking since its just activation flag)
     * @return boolean True when succeed.
     */
    public function activateProductIdList(array $productListId, $activate = true)
    {
        $failedIdList = $productListId; // since only one update is done, we cannot keep only fails for now.
        // TODO
        throw new WarningException('Cannot change activation state many requested products.', $failedIdList);
        return true;
    }

    /**
     * Do a safe delete on given product IDs
     *
     * @param array $productListId The ID list of products to delete
     * @throws ErrorException If an error occured during deletion
     * @return boolean
     */
    public function deleteProductIdList(array $productListId)
    {
        $failedIdList = $productListId; // since only one update is done, we cannot keep only fails for now.
        // TODO: from $productIdList (id_product(s)), do a safe delete (ORM?)
        throw new ErrorException('Cannot delete many requested products.', $failedIdList);
        return true;
    }
}
