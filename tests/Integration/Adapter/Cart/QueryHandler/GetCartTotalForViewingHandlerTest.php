<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Adapter\Cart\QueryHandler;

use Context;
use PrestaShop\PrestaShop\Core\Domain\Cart\Query\GetCartForViewing;
use PrestaShop\PrestaShop\Core\Domain\Cart\Query\GetCartTotalForViewing;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * The carts grids fill the total column of every row with GetCartTotalForViewing instead of the
 * full GetCartForViewing. This guards that the lightweight query keeps returning the exact same
 * total as the full cart view.
 */
class GetCartTotalForViewingHandlerTest extends KernelTestCase
{
    public function testItReturnsTheSameTotalAsTheFullCartView(): void
    {
        self::bootKernel();
        $container = self::getContainer();
        // getOrderTotal() reaches for the container through the legacy context.
        Context::getContext()->container = $container;
        $queryBus = $container->get('prestashop.core.query_bus');
        $connection = $container->get('doctrine.dbal.default_connection');

        $cartIds = $connection
            ->executeQuery('SELECT id_cart FROM ' . _DB_PREFIX_ . 'cart WHERE id_customer > 0 ORDER BY id_cart')
            ->fetchFirstColumn();

        $compared = 0;
        foreach ($cartIds as $cartId) {
            $cartId = (int) $cartId;

            $fullView = $queryBus->handle(new GetCartForViewing($cartId))->getCartSummary();
            $light = $queryBus->handle(new GetCartTotalForViewing($cartId));

            $this->assertSame(
                (float) $fullView['total'],
                (float) $light->getTotal(),
                sprintf('Total mismatch for cart %d', $cartId)
            );
            $this->assertSame(
                (float) $fullView['total_products'],
                (float) $light->getTotalProducts(),
                sprintf('Products total mismatch for cart %d', $cartId)
            );
            ++$compared;
        }

        $this->assertGreaterThan(0, $compared, 'Expected at least one cart to compare.');
    }
}
