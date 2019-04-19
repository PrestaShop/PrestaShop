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

namespace PrestaShop\PrestaShop\Adapter\Supplier\CommandHandler;

use DateTime;
use PrestaShopDatabaseException;
use PrestaShopException;
use Supplier;
use PrestaShop\PrestaShop\Adapter\Supplier\AbstractSupplierHandler;
use PrestaShop\PrestaShop\Core\Domain\Supplier\Command\AddSupplierCommand;
use PrestaShop\PrestaShop\Core\Domain\Supplier\CommandHandler\AddSupplierHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Supplier\Exception\SupplierException;
use PrestaShop\PrestaShop\Core\Domain\Supplier\ValueObject\SupplierId;

/**
 * Handles command which adds new supplier using legacy object model
 */
final class AddSupplierHandler extends AbstractSupplierHandler implements AddSupplierHandlerInterface
{
    /**
     * {@inheritdoc}
     */
    public function handle(AddSupplierCommand $command)
    {
        $supplier = new Supplier();
        $this->fillLegacySupplierWithData($supplier, $command);

        try {
            if (false === $supplier->validateFields(false)) {
                throw new SupplierException('Supplier contains invalid field values');
            }

            if (!$supplier->add()) {
                throw new SupplierException(
                    sprintf('Failed to add new supplier "%s"', $command->getName())
                );
            }
            $this->addShopAssociation($supplier, $command);
        } catch (PrestaShopException $e) {
            throw new SupplierException(
                sprintf('Failed to add new supplier "%s"', $command->getName())
            );
        }

        return new SupplierId((int) $supplier->id);
    }

    /**
     * Add supplier and shop association
     *
     * @param Supplier $supplier
     * @param AddSupplierCommand $command
     *
     * @throws PrestaShopDatabaseException
     */
    private function addShopAssociation(Supplier $supplier, AddSupplierCommand $command)
    {
        $this->associateWithShops(
            $supplier,
            $command->getShopAssociation()
        );
    }

    /**
     * @param Supplier $supplier
     * @param AddSupplierCommand $command
     */
    private function fillLegacySupplierWithData(Supplier $supplier, AddSupplierCommand $command)
    {
        $currentDateTime = (new DateTime())->format('Y-m-d H:i:s'); //@todo: check time zone and format

        $supplier->name = $command->getName();
        $supplier->description = $command->getLocalizedDescriptions();
        $supplier->meta_description = $command->getLocalizedMetaDescriptions();
        $supplier->meta_title = $command->getLocalizedMetaTitles();
        $supplier->meta_keywords = $command->getLocalizedMetaKeywords();
        $supplier->date_add = $currentDateTime;
        $supplier->date_upd = $currentDateTime;
        //@todo: check Supplier link_rewrite usability
        $supplier->active = $command->isEnabled();
    }
}
