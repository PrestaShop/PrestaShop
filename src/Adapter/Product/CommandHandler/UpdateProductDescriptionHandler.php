<?php
/**
 * 2007-2020 PrestaShop SA and Contributors
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
 * @copyright 2007-2020 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Product\CommandHandler;

use PrestaShop\PrestaShop\Adapter\Product\AbstractProductHandler;
use PrestaShop\PrestaShop\Core\Domain\Product\Command\UpdateProductDescriptionCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\CommandHandler\UpdateProductDescriptionHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\CannotUpdateProductException;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\ProductConstraintException;
use PrestaShopException;
use Product;

/**
 * Handles UpdateProductDescriptionCommand using legacy object model
 */
final class UpdateProductDescriptionHandler extends AbstractProductHandler implements UpdateProductDescriptionHandlerInterface
{
    /** @var string Product description property name */
    private const DESCRIPTION = 'description';

    /** @var string Product short description property name */
    private const DESCRIPTION_SHORT = 'description_short';

    /**
     * {@inheritdoc}
     */
    public function handle(UpdateProductDescriptionCommand $command): void
    {
        $productId = $command->getProductId();
        $product = $this->getProduct($productId);

        $localizedDescriptions = $command->getLocalizedDescriptions();
        $localizedShortDescriptions = $command->getLocalizedShortDescriptions();

        if (null !== $localizedDescriptions) {
            $this->validate($product, $localizedDescriptions, self::DESCRIPTION);
            $product->description = $localizedDescriptions;
        }

        if (null !== $localizedShortDescriptions) {
            $this->validate($product, $localizedShortDescriptions, self::DESCRIPTION_SHORT);
            $product->description_short = $localizedShortDescriptions;
        }

        try {
            if (false === $product->update()) {
                throw new CannotUpdateProductException(sprintf(
                    'Error occurred when trying to update product #%s description',
                    $product->id
                ));
            }
        } catch (PrestaShopException $e) {
            throw new CannotUpdateProductException(
                sprintf(
                    'Error occurred when trying to update product #%s description',
                    $product->id
                ),
                0,
                $e
            );
        }
    }

    /**
     * @param Product $product
     * @param array $localizedValues
     * @param string $propertyName
     *
     * @throws ProductConstraintException
     * @throws PrestaShopException
     */
    private function validate(Product $product, array $localizedValues, string $propertyName): void
    {
        if (self::DESCRIPTION === $propertyName) {
            $errorCode = ProductConstraintException::INVALID_DESCRIPTION;
        } else {
            $errorCode = ProductConstraintException::INVALID_SHORT_DESCRIPTION;
        }

        foreach ($localizedValues as $langId => $localizedValue) {
            if (true !== $product->validateField($propertyName, $localizedValue, $langId)) {
                throw new ProductConstraintException(
                    sprintf(
                        'Invalid %s "%s" in language with id "%s"',
                        $propertyName,
                        $localizedValue,
                        $langId
                    ),
                    $errorCode
                );
            }
        }
    }
}
