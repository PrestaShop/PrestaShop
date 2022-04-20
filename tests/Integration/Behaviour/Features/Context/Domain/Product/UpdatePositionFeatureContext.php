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

namespace Tests\Integration\Behaviour\Features\Context\Domain\Product;

use Behat\Gherkin\Node\TableNode;
use Carrier;
use Doctrine\DBAL\Driver\Exception;
use PHPUnit\Framework\Assert;
use PrestaShop\Decimal\DecimalNumber;
use PrestaShop\PrestaShop\Core\Domain\Category\ValueObject\CategoryId;
use PrestaShop\PrestaShop\Core\Domain\Product\Command\UpdateProductPositionCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Command\UpdateProductShippingCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\ProductConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\ProductException;
use PrestaShop\PrestaShop\Core\Domain\Product\QueryResult\ProductShippingInformation;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\DeliveryTimeNoteType;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;
use PrestaShop\PrestaShop\Core\Grid\Position\Exception\PositionDataException;
use PrestaShop\PrestaShop\Core\Grid\Position\Exception\PositionException;
use RuntimeException;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Tests\Integration\Behaviour\Features\Context\CommonFeatureContext;
use Tests\Integration\Behaviour\Features\Context\Util\PrimitiveUtils;

class UpdatePositionFeatureContext extends AbstractProductFeatureContext
{
    /**
     * @When I update product position in category :category with following values:
     *
     * @param string $categoryReference
     * @param TableNode $table
     */
    public function updateProductPosition(string $categoryReference, TableNode $table): void
    {
        $data = $this->localizeByColumns($table);
        $positions = [];
        foreach ($data as $datum) {
            $productId = $this->getSharedStorage()->get($datum['product_reference']);
            $positions[$productId] = [
                'rowId' => $productId,
                'oldPosition' => $datum['old_position'],
                'newPosition' => $datum['new_position'],
            ];
        }
        try {
            $command = new UpdateProductPositionCommand($positions, $this->getSharedStorage()->get($categoryReference));
            $this->getCommandBus()->handle($command);
        } catch (PositionException $e) {
            $this->setLastException($e);
        }
    }

    /**
     * @Then positions should be assigned accordingly:
     *
     * @param TableNode $tableNode
     *
     * @throws Exception
     * @throws \Doctrine\DBAL\Exception
     * @throws ProductConstraintException
     */
    public function assertPositionInformation(TableNode $tableNode): void
    {
        $products = $this->localizeByColumns($tableNode);
        $productRepository = CommonFeatureContext::getContainer()->get('prestashop.adapter.product.repository.product_repository');
        foreach ($products as $product) {
            $productId = new ProductId($this->getSharedStorage()->get($product['product_reference']));
            $categoryId = new CategoryId($this->getSharedStorage()->get($product['category_reference']));
            Assert::assertSame((int) $product['position'], $productRepository->getPositionInCategory($productId, $categoryId));
        }
    }
}
