<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\PrestaShopBundle\Controller\Admin\Improve\International;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Adapter\LegacyContext;
use PrestaShop\PrestaShop\Core\ConfigurationInterface;
use PrestaShop\PrestaShop\Core\Form\FormHandlerInterface;
use PrestaShop\PrestaShop\Core\Hook\HookDispatcherInterface;
use PrestaShop\PrestaShop\Core\Kpi\Row\HookableKpiRowFactory;
use PrestaShop\PrestaShop\Core\Kpi\Row\KpiRowInterface;
use PrestaShopBundle\Controller\Admin\Improve\International\TranslationsController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class TranslationsKpiVisibilityTest extends TestCase
{
    /**
     * @dataProvider provideVisibility
     */
    public function testListDoesNotBuildDisabledKpis(mixed $storedValue, bool $enabled): void
    {
        $controller = $this->getMockBuilder(TranslationsController::class)->disableOriginalConstructor()->onlyMethods([
            'getConfiguration', 'generateSidebarLink', 'trans', 'render',
        ])->getMock();
        $configuration = $this->createMock(ConfigurationInterface::class);
        $configuration->expects(self::once())->method('get')->with('PS_DISPLAY_BACKOFFICE_KPIS')->willReturn($storedValue);
        $controller->method('getConfiguration')->willReturn($configuration);
        $controller->method('generateSidebarLink')->willReturn('');
        $controller->method('trans')->willReturn('');
        $form = $this->createMock(FormInterface::class);
        $form->method('createView')->willReturn(new FormView());
        $controller->expects(self::once())->method('render')->willReturnCallback(
            function (string $view, array $parameters) use ($enabled): Response {
                $row = $parameters['kpiRow'];
                if ($enabled) {
                    self::assertInstanceOf(KpiRowInterface::class, $row);
                } else {
                    self::assertNull($row);
                }

                return new Response();
            }
        );
        $hooks = $this->createMock(HookDispatcherInterface::class);
        $hooks->expects($enabled ? self::once() : self::never())->method('dispatchWithParameters');
        $factory = new HookableKpiRowFactory([], $hooks, 'translations');
        $handler = $this->createMock(FormHandlerInterface::class);
        $handler->method('getForm')->willReturn($form);
        $legacyContext = $this->createMock(LegacyContext::class);
        $legacyContext->method('getAdminLink')->willReturn('/');
        $request = new Request();
        $request->attributes->set('_legacy_controller', 'AdminTranslations');
        $controller->showSettingsAction($request, $handler, $handler, $handler, $handler, $legacyContext, $factory);
    }

    public static function provideVisibility(): iterable
    {
        yield 'missing setting' => [null, true];
        yield 'enabled' => ['1', true];
        yield 'disabled' => ['0', false];
    }
}
