<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\PrestaShopBundle\Twig\Component;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Adapter\LegacyContext;
use PrestaShop\PrestaShop\Adapter\Module\Tab\ModuleTabRegister;
use PrestaShopBundle\Twig\Component\NavBar;
use PrestaShopBundle\Twig\Layout\MenuBuilder;
use Psr\Log\LoggerInterface;
use ReflectionMethod;

class NavBarTest extends TestCase
{
    /**
     * @dataProvider provideTopLevelSections
     *
     * @param array<string, mixed> $tab
     */
    public function testATopLevelSectionIsKeptOnlyWhenItLeadsSomewhere(array $tab, bool $expected, string $case): void
    {
        $navBar = new NavBar(
            $this->createMock(LegacyContext::class),
            $this->createMock(LoggerInterface::class),
            $this->createMock(MenuBuilder::class),
            '9.1.0'
        );

        $method = new ReflectionMethod(NavBar::class, 'isUnreachableTopLevelSection');
        $method->setAccessible(true);

        $this->assertSame($expected, $method->invoke($navBar, $tab), $case);
    }

    /**
     * @return iterable<string, array{0: array<string, mixed>, 1: bool, 2: string}>
     */
    public function provideTopLevelSections(): iterable
    {
        yield 'catch-all parent with an icon' => [
            ['id_parent' => 0, 'icon' => 'extension', 'class_name' => ModuleTabRegister::DEFAULT_PARENT_CLASS_NAME],
            true,
            'The catch-all parent owns no page, so an icon must not keep it in the menu',
        ];

        yield 'catch-all parent without an icon' => [
            ['id_parent' => 0, 'icon' => '', 'class_name' => ModuleTabRegister::DEFAULT_PARENT_CLASS_NAME],
            true,
            'Already dropped before, and still dropped',
        ];

        yield 'section owning a page, with an icon' => [
            ['id_parent' => 0, 'icon' => 'shopping_cart', 'class_name' => 'AdminParentOrders'],
            false,
            'A real top level section with an icon is kept',
        ];

        yield 'section owning a page, without an icon' => [
            ['id_parent' => 0, 'icon' => '', 'class_name' => 'AdminParentOrders'],
            true,
            'Unchanged behaviour: no icon and no linkable child means nothing to show',
        ];

        yield 'child tab' => [
            ['id_parent' => 12, 'icon' => '', 'class_name' => ModuleTabRegister::DEFAULT_PARENT_CLASS_NAME],
            false,
            'The rule is about top level entries only',
        ];
    }
}
