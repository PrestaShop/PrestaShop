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
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Product\Customization\CommandHandler;

use PrestaShop\PrestaShop\Adapter\Product\Customization\Update\CustomizationFieldDeleter;
use PrestaShop\PrestaShop\Adapter\Product\Customization\Update\ProductCustomizationFieldUpdater;
use PrestaShop\PrestaShop\Adapter\Product\Repository\ProductRepository;
use PrestaShop\PrestaShop\Core\Domain\Product\Customization\Command\RemoveAllCustomizationFieldsFromProductCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Customization\CommandHandler\RemoveAllCustomizationFieldsFromProductHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\Customization\ValueObject\CustomizationFieldId;

/**
 * Handles @see RemoveAllCustomizationFieldsFromProductCommand using legacy object model
 */
final class RemoveAllCustomizationFieldsFromProductHandler implements RemoveAllCustomizationFieldsFromProductHandlerInterface
{
    /**
     * @var CustomizationFieldDeleter
     */
    private $customizationFieldDeleter;

    /**
     * @var ProductRepository
     */
    private $productRepository;

    /**
     * @var ProductCustomizationFieldUpdater
     */
    private $productCustomizationFieldUpdater;

    /**
     * @param CustomizationFieldDeleter $customizationFieldDeleter
     * @param ProductRepository $productRepository
     * @param ProductCustomizationFieldUpdater $productCustomizationFieldUpdater
     */
    public function __construct(
        CustomizationFieldDeleter $customizationFieldDeleter,
        ProductRepository $productRepository,
        ProductCustomizationFieldUpdater $productCustomizationFieldUpdater
    ) {
        $this->customizationFieldDeleter = $customizationFieldDeleter;
        $this->productRepository = $productRepository;
        $this->productCustomizationFieldUpdater = $productCustomizationFieldUpdater;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(RemoveAllCustomizationFieldsFromProductCommand $command): void
    {
        $product = $this->productRepository->getProductByDefaultShop($command->getProductId());

        $customizationFieldIds = array_map(function (array $field): CustomizationFieldId {
            return new CustomizationFieldId((int) $field['id_customization_field']);
        }, $product->getCustomizationFieldIds());

        $this->customizationFieldDeleter->bulkDelete($customizationFieldIds);
        $this->productCustomizationFieldUpdater->refreshProductCustomizability($command->getProductId());
    }
}
