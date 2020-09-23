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

use PrestaShop\PrestaShop\Adapter\Product\AbstractProductHandler;
use PrestaShop\PrestaShop\Adapter\Product\ProductPersister;
use PrestaShop\PrestaShop\Core\Domain\Product\Command\AddProductCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\CommandHandler\AddProductHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;
use Product;

/**
 * Handles @AddProductCommand using legacy object model
 */
final class AddProductHandler extends AbstractProductHandler implements AddProductHandlerInterface
{
    /**
     * @var int
     */
    private $defaultLangId;

    /**
     * @var int
     */
    private $defaultCategoryId;

    /**
     * @var ProductPersister
     */
    private $productPersister;

    /**
     * @param int $defaultLangId
     * @param int $defaultCategoryId
     * @param ProductPersister $productPersister
     */
    public function __construct(
        int $defaultLangId,
        int $defaultCategoryId,
        ProductPersister $productPersister
    ) {
        $this->defaultLangId = $defaultLangId;
        $this->defaultCategoryId = $defaultCategoryId;
        $this->productPersister = $productPersister;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(AddProductCommand $command): ProductId
    {
        $product = new Product();

        $product->name = $command->getLocalizedNames();
        $product->active = false;
        $product->id_category_default = $this->defaultCategoryId;
        $product->is_virtual = $command->isVirtual();

        $this->productPersister->add($product);

        return new ProductId((int) $product->id);
    }
}
