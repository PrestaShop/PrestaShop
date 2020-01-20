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

namespace Tests\Integration\Behaviour\Features\Context\Domain;

use Behat\Gherkin\Node\TableNode;
use PrestaShop\PrestaShop\Core\Domain\Order\Command\CancelOrderProductCommand;
use PrestaShop\PrestaShop\Core\Domain\Order\Exception\EmptyCancelQuantityException;
use PrestaShop\PrestaShop\Core\Domain\Order\Exception\OrderException;
use PrestaShop\PrestaShop\Core\Domain\Order\Query\GetOrderForViewing;
use Tests\Integration\Behaviour\Features\Context\SharedStorage;

class OrderCancelProductFeatureContext extends AbstractDomainFeatureContext
{
    /**
     * @When I do a cancel from order :orderReference on the following products:
     *
     * @param string $orderReference
     * @param TableNode $table
     */
    public function cancelOrderProduct(string $orderReference, TableNode $table)
    {
        $cancelProductInfos = $table->getColumnsHash();
        $orderId = SharedStorage::getStorage()->get($orderReference);
        $orderForViewing = $this->getQueryBus()->handle(new GetOrderForViewing((int) $orderId));
        $products = $orderForViewing->getProducts()->getProducts();
        $toBeCanceledProducts = [];

        foreach ($cancelProductInfos as $cancelledProductInfo) {
            foreach ($products as $product) {
                if ($product->getName() === $cancelledProductInfo['product_name']) {
                    $toBeCanceledProducts[$product->getOrderDetailId()] = $cancelledProductInfo['quantity'];
                }
            }
        }
        try {
            $command = new CancelOrderProductCommand(
                $products,
                $toBeCanceledProducts,
                $orderForViewing
            );

            $this->getCommandBus()->handle($command);
        } catch (OrderException $e) {
            $this->lastException = $e;
        }
    }

    /**
     * @Then I should get error that cancel quantity is empty
     */
    public function assertLastErrorIsEmptyCancelQuantity()
    {
        $this->assertLastErrorIs(EmptyCancelQuantityException::class);
    }
}
