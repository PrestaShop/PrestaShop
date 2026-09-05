<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\PrestaShopBundle\Twig\Component;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Action\ActionsBarButtonsCollection;
use PrestaShop\PrestaShop\Core\Context\EmployeeContext;
use PrestaShop\PrestaShop\Core\Hook\HookDispatcherInterface;
use PrestaShopBundle\Twig\Component\EmployeeDropdown;

/**
 * The employee menu hook stopped firing because both component templates read the bare
 * `displayBackOfficeEmployeeMenu`, which resolves to the component's own null property, instead of
 * `this.displayBackOfficeEmployeeMenu`, which goes through the getter that dispatches the hook.
 *
 * @see https://github.com/PrestaShop/PrestaShop/issues/38359
 */
class EmployeeDropdownTest extends TestCase
{
    private const TEMPLATES = [
        'src/PrestaShopBundle/Resources/views/Admin/Component/Layout/employee_dropdown.html.twig',
        'src/PrestaShopBundle/Resources/views/Admin/Component/LegacyLayout/employee_dropdown.html.twig',
    ];

    public function testTheGetterDispatchesTheHookAndReturnsTheCollection(): void
    {
        $dispatched = [];
        $hookDispatcher = $this->createMock(HookDispatcherInterface::class);
        $hookDispatcher
            ->expects($this->once())
            ->method('dispatchWithParameters')
            ->willReturnCallback(function (string $hookName, array $parameters) use (&$dispatched) {
                $dispatched[] = $hookName;
                $this->assertInstanceOf(ActionsBarButtonsCollection::class, $parameters['links']);
            });

        $component = new EmployeeDropdown($hookDispatcher, $this->createMock(EmployeeContext::class));

        $collection = $component->getDisplayBackOfficeEmployeeMenu();

        $this->assertSame(['displayBackOfficeEmployeeMenu'], $dispatched);
        $this->assertInstanceOf(ActionsBarButtonsCollection::class, $collection);
    }

    public function testTheHookIsDispatchedOnlyOnce(): void
    {
        $hookDispatcher = $this->createMock(HookDispatcherInterface::class);
        $hookDispatcher->expects($this->once())->method('dispatchWithParameters');

        $component = new EmployeeDropdown($hookDispatcher, $this->createMock(EmployeeContext::class));

        $first = $component->getDisplayBackOfficeEmployeeMenu();
        $this->assertSame($first, $component->getDisplayBackOfficeEmployeeMenu());
    }

    /**
     * The regression that actually happened: a template reading the member without `this.` gets the
     * property, never the getter, so the hook is never dispatched and the loop renders nothing.
     */
    public function testBothTemplatesReachTheMemberThroughTheComponent(): void
    {
        foreach (self::TEMPLATES as $template) {
            $contents = file_get_contents(_PS_ROOT_DIR_ . '/' . $template);
            $this->assertIsString($contents, $template . ' could not be read');

            $this->assertStringContainsString(
                'this.displayBackOfficeEmployeeMenu',
                $contents,
                $template . ' must read the member through the component, or the hook is never dispatched'
            );
            $this->assertDoesNotMatchRegularExpression(
                '/(?<!this\.)\bdisplayBackOfficeEmployeeMenu\b/',
                preg_replace('/this\.displayBackOfficeEmployeeMenu/', '', $contents),
                $template . ' still reads the bare property name'
            );
        }
    }
}
