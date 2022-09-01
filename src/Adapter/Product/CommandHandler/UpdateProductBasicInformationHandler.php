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

namespace PrestaShop\PrestaShop\Adapter\Product\CommandHandler;

use PrestaShop\PrestaShop\Adapter\Product\Repository\ProductMultiShopRepository;
use PrestaShop\PrestaShop\Adapter\Tools;
use PrestaShop\PrestaShop\Core\Domain\Product\Command\UpdateProductBasicInformationCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\CommandHandler\UpdateProductBasicInformationHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\CannotUpdateProductException;
use Product;

/**
 * Handles command for product basic information update using legacy object model
 */
class UpdateProductBasicInformationHandler implements UpdateProductBasicInformationHandlerInterface
{
    /**
     * @var ProductMultiShopRepository
     */
    private $productRepository;

    /**
     * @var int
     */
    private $defaultLanguageId;

    /**
     * @var Tools
     */
    private $tools;

    /**
     * @param ProductMultiShopRepository $productRepository
     * @param int $defaultLanguageId
     * @param Tools $tools
     */
    public function __construct(
        ProductMultiShopRepository $productRepository,
        int $defaultLanguageId,
        Tools $tools
    ) {
        $this->productRepository = $productRepository;
        $this->defaultLanguageId = $defaultLanguageId;
        $this->tools = $tools;
    }

    /**
     * {@inheritdoc}
     *
     * Null values are not updated, because are considered unchanged
     */
    public function handle(UpdateProductBasicInformationCommand $command): void
    {
        $product = $this->productRepository->getByShopConstraint($command->getProductId(), $command->getShopConstraint());
        $updatableProperties = $this->fillUpdatableProperties($product, $command);

        if (empty($updatableProperties)) {
            return;
        }

        $this->productRepository->partialUpdate(
            $product,
            $updatableProperties,
            $command->getShopConstraint(),
            CannotUpdateProductException::FAILED_UPDATE_BASIC_INFO
        );
    }

    /**
     * @param Product $product
     * @param UpdateProductBasicInformationCommand $command
     *
     * @return array<string, mixed>
     */
    private function fillUpdatableProperties(
        Product $product,
        UpdateProductBasicInformationCommand $command
    ): array {
        $updatableProperties = [];

        $localizedNames = $command->getLocalizedNames();
        if (null !== $localizedNames) {
            $defaultName = $localizedNames[$this->defaultLanguageId];
            // Go through all the product languages and make sure name is filled for each of them
            $productLanguages = array_keys($product->name);
            foreach ($productLanguages as $languageId) {
                if (empty($localizedNames[$languageId])) {
                    $localizedNames[$languageId] = $defaultName;
                }
            }
            $product->name = $localizedNames;
            $updatableProperties['name'] = array_keys($localizedNames);
        }

        foreach ($product->link_rewrite as $langId => $linkRewrite) {
            if (!empty($linkRewrite) || empty($product->name[$langId])) {
                continue;
            }

            $product->link_rewrite[$langId] = $this->tools->linkRewrite($product->name[$langId]);
            $updatableProperties['link_rewrite'][] = $langId;
        }

        $localizedDescriptions = $command->getLocalizedDescriptions();
        if (null !== $localizedDescriptions) {
            $product->description = $localizedDescriptions;
            $updatableProperties['description'] = array_keys($localizedDescriptions);
        }

        $localizedShortDescriptions = $command->getLocalizedShortDescriptions();
        if (null !== $localizedShortDescriptions) {
            $product->description_short = $localizedShortDescriptions;
            $updatableProperties['description_short'] = array_keys($localizedShortDescriptions);
        }

        return $updatableProperties;
    }
}
