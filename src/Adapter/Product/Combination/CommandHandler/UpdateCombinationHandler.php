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

namespace PrestaShop\PrestaShop\Adapter\Product\Combination\CommandHandler;

use PrestaShop\Decimal\DecimalNumber;
use PrestaShop\PrestaShop\Adapter\Product\Combination\Repository\CombinationRepository;
use PrestaShop\PrestaShop\Adapter\Product\Combination\Update\DefaultCombinationUpdater;
use PrestaShop\PrestaShop\Adapter\Product\Combination\Update\Filler\CombinationFillerInterface;
use PrestaShop\PrestaShop\Adapter\Product\Repository\ProductSupplierRepository;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\Command\UpdateCombinationCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\CommandHandler\UpdateCommandHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\Exception\CannotUpdateCombinationException;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\ValueObject\CombinationId;
use PrestaShop\PrestaShop\Core\Domain\Product\Supplier\ValueObject\ProductSupplierAssociation;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;

/**
 * Handles the @see UpdateCombinationCommand using legacy object model
 */
class UpdateCombinationHandler implements UpdateCommandHandlerInterface
{
    /**
     * @var CombinationRepository
     */
    private $combinationRepository;

    /**
     * @var CombinationFillerInterface
     */
    private $combinationFiller;

    /**
     * @var ProductSupplierRepository
     */
    private $productSupplierRepository;

    /**
     * @var DefaultCombinationUpdater
     */
    private $defaultCombinationUpdater;

    public function __construct(
        CombinationRepository $combinationRepository,
        CombinationFillerInterface $combinationFiller,
        ProductSupplierRepository $productSupplierRepository,
        DefaultCombinationUpdater $defaultCombinationUpdater
    ) {
        $this->combinationRepository = $combinationRepository;
        $this->combinationFiller = $combinationFiller;
        $this->productSupplierRepository = $productSupplierRepository;
        $this->defaultCombinationUpdater = $defaultCombinationUpdater;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(UpdateCombinationCommand $command): void
    {
        $combination = $this->combinationRepository->get($command->getCombinationId());
        $updatableProperties = $this->combinationFiller->fillUpdatableProperties($combination, $command);

        $this->combinationRepository->partialUpdate(
            $combination,
            $updatableProperties,
            CannotUpdateCombinationException::FAILED_UPDATE_COMBINATION
        );

        // Only update default if the property is set AND is true
        if (true === $command->isDefault()) {
            $this->defaultCombinationUpdater->setDefaultCombination(
                $command->getCombinationId(),
                // @todo: temporary hardcoded shop constraint. Needs to be required in command constructor.
                ShopConstraint::shop((int) \Context::getContext()->shop->id)
            );
        }

        if (null !== $command->getWholesalePrice()) {
            $this->updateDefaultSupplier($command->getCombinationId(), $command->getWholesalePrice());
        }
    }

    private function updateDefaultSupplier(CombinationId $combinationId, DecimalNumber $wholesalePrice): void
    {
        $productId = $this->combinationRepository->getProductId($combinationId);
        $defaultSupplierId = $this->productSupplierRepository->getDefaultSupplierId($productId);
        if (null === $defaultSupplierId) {
            return;
        }

        $defaultProductSupplierId = $this->productSupplierRepository->getIdByAssociation(new ProductSupplierAssociation(
            $productId->getValue(),
            $combinationId->getValue(),
            $defaultSupplierId->getValue()
        ));
        if (!$defaultProductSupplierId) {
            return;
        }

        $defaultProductSupplier = $this->productSupplierRepository->get($defaultProductSupplierId);
        $defaultProductSupplier->product_supplier_price_te = (float) (string) $wholesalePrice;
        $this->productSupplierRepository->update($defaultProductSupplier);
    }
}
