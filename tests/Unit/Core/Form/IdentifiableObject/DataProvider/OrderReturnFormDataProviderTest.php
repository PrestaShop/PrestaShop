<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Core\Form\IdentifiableObject\DataProvider;

use DateTimeImmutable;
use Generator;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Domain\OrderReturn\QueryResult\OrderReturnForEditing;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataProvider\OrderReturnFormDataProvider;
use PrestaShopBundle\Service\Routing\Router;
use RuntimeException;
use Symfony\Contracts\Translation\TranslatorInterface;

class OrderReturnFormDataProviderTest extends TestCase
{
    /**
     * @dataProvider getExpectedChoices
     */
    public function testBuildOrderReturnInformation(OrderReturnForEditing $orderReturnForEditing, array $expectedResult): void
    {
        $formDataProvider = new OrderReturnFormDataProvider(
            $this->createQueryBusMock($orderReturnForEditing),
            $this->createRouterMock(),
            $this->createTranslatorMock(),
            'Y-m-d'
        );

        $data = $formDataProvider->getData($orderReturnForEditing->getOrderReturnId());

        $this->assertEquals($expectedResult, $data);
    }

    /**
     * @param OrderReturnForEditing $orderReturnForEditing
     *
     * @return CommandBusInterface
     */
    private function createQueryBusMock(OrderReturnForEditing $orderReturnForEditing): CommandBusInterface
    {
        $queryBusMock = $this->createMock(CommandBusInterface::class);

        $queryBusMock
            ->method('handle')
            ->willReturn($orderReturnForEditing);

        return $queryBusMock;
    }

    /**
     * @return Router
     */
    private function createRouterMock(): Router
    {
        $routerMock = $this->createMock(Router::class);

        $routerMock
            ->method('generate')
            ->willReturnCallback(function ($route, $arguments) {
                return $this->createResultBasedOnQuery($route, $arguments);
            });

        return $routerMock;
    }

    /**
     * @return TranslatorInterface
     */
    private function createTranslatorMock(): TranslatorInterface
    {
        $translatorMock = $this->createMock(TranslatorInterface::class);

        $translatorMock
            ->method('trans')
            ->willReturnMap(
                [
                    [
                        '#%order_id% from %order_date%',
                        [
                            '%order_id%' => 3,
                            '%order_date%' => '2020-02-22',
                        ],
                        'Admin.Orderscustomers.Feature',
                        null,
                        '#3 from 2020-02-22',
                    ],
                ]
            );

        return $translatorMock;
    }

    /**
     * @return Generator
     */
    public function getExpectedChoices(): Generator
    {
        yield [
            new OrderReturnForEditing(
                1,
                2,
                'John',
                'Doe',
                3,
                new DateTimeImmutable('2020-02-22'),
                4,
                'question'
            ),
            [
                'question' => 'question',
                'customer_name' => 'John Doe',
                'customer_link' => 'customer_2',
                'order' => '#3 from 2020-02-22',
                'order_link' => 'order_3',
                'order_return_state' => 4,
            ],
        ];
    }

    /**
     * @param string $route
     * @param array $arguments
     *
     * @return string
     */
    private function createResultBasedOnQuery(string $route, array $arguments): string
    {
        switch ($route) {
            case 'admin_customers_view':
                return 'customer_' . $arguments['customerId'];
            case 'admin_orders_view':
                return 'order_' . $arguments['orderId'];
        }

        throw new RuntimeException(sprintf('Route "%s" was not expected in query bus mock', $route));
    }
}
