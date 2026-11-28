<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\PrestaShopBundle\Twig\Component\Login;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Context\LegacyControllerContext;
use PrestaShop\PrestaShop\Core\Hook\HookDispatcherInterface;
use PrestaShopBundle\Twig\Component\HeadTag;
use PrestaShopBundle\Twig\Component\Login\LoginHeadTag;
use PrestaShopBundle\Twig\Component\Login\TwoFactorHeadTag;
use ReflectionClass;
use ReflectionProperty;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

class LoginHeadTagTest extends TestCase
{
    #[DataProvider('componentHooks')]
    public function testMountDispatchesOnlyItsOwnMediaHook(string $componentClass, string $hookName): void
    {
        $controller = (new ReflectionClass(LegacyControllerContext::class))->newInstanceWithoutConstructor();
        $hookDispatcher = $this->createMock(HookDispatcherInterface::class);
        $hookDispatcher->expects($this->once())
            ->method('dispatchWithParameters')
            ->with($hookName, ['controller' => $controller]);

        $component = (new ReflectionClass($componentClass))->newInstanceWithoutConstructor();
        (new ReflectionProperty(HeadTag::class, 'hookDispatcher'))->setValue($component, $hookDispatcher);
        (new ReflectionProperty(HeadTag::class, 'legacyControllerContext'))->setValue($component, $controller);

        $component->mount('Test');

        $this->assertSame('Test', $component->getMetaTitle());
    }

    #[DataProvider('componentHooks')]
    public function testComponentUsesLoginHeadTemplate(string $componentClass): void
    {
        $attributes = (new ReflectionClass($componentClass))->getAttributes(AsTwigComponent::class);

        $this->assertCount(1, $attributes);
        $this->assertSame(
            '@PrestaShop/Admin/Component/LoginLayout/head_tag.html.twig',
            $attributes[0]->newInstance()->serviceConfig()['template']
        );
    }

    public static function componentHooks(): iterable
    {
        yield 'login' => [LoginHeadTag::class, 'actionAdminLoginControllerSetMedia'];
        yield 'two-factor' => [TwoFactorHeadTag::class, 'actionAdminTwoFactorControllerSetMedia'];
    }
}
