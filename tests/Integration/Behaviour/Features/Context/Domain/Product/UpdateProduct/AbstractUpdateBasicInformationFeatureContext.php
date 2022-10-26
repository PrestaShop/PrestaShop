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

namespace Tests\Integration\Behaviour\Features\Context\Domain\Product\UpdateProduct;

use Behat\Gherkin\Node\TableNode;
use Product;
use Tests\Integration\Behaviour\Features\Context\Domain\Product\AbstractProductFeatureContext;

/**
 * This abstract class was introduced during UpdateProductCommand unification process,
 * and which idea is to remove multiple sub-commands and use single UpdateProductCommand instead.
 * This abstract context allows sharing assertions which and some other common methods for both implementations during the transition.
 *
 * @see UpdateProductCommand
 * @see UpdateProductHandlerInterface
 *
 * @todo: need to check if this abstract class is still needed when UpdateProductCommand is fully finished,
 *        because one of the contexts that uses it will be deleted, therefore leaving this abstract class useless.
 */
abstract class AbstractUpdateBasicInformationFeatureContext extends AbstractProductFeatureContext
{
    /**
     * This method is created just for specific cases when product name needs to be updated
     * using legacy object model, but not cqrs commands, to avoid some side effects while testing.
     * For example when testing how cqrs command auto-fills link_rewrite in certain cases.
     */
    protected function updateProductNameManually(string $productReference, TableNode $table): void
    {
        $productId = $this->getSharedStorage()->get($productReference);
        $product = new Product($productId, true);
        $product->name = $this->localizeByRows($table)['name'];

        $product->update();
    }
}
