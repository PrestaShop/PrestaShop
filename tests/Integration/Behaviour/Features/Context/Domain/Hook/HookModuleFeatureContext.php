<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Behaviour\Features\Context\Domain\Hook;

use Db;
use Hook;
use Module;
use PHPUnit\Framework\Assert;
use PrestaShop\PrestaShop\Core\Domain\Hook\Command\EditHookedModuleCommand;
use PrestaShop\PrestaShop\Core\Domain\Hook\Command\HookModuleCommand;
use PrestaShop\PrestaShop\Core\Domain\Hook\Exception\CannotUpdateHookException;
use PrestaShop\PrestaShop\Core\Domain\Hook\Exception\HookException;
use PrestaShop\PrestaShop\Core\Domain\Hook\Exception\HookNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Hook\Exception\ModuleAlreadyHookedException;
use PrestaShop\PrestaShop\Core\Domain\Hook\Exception\ModuleCannotBeHookedException;
use PrestaShop\PrestaShop\Core\Domain\Hook\Query\GetPossibleHooksForModule;
use PrestaShop\PrestaShop\Core\Domain\Hook\QueryResult\HookableInfo;
use RuntimeException;
use Tests\Integration\Behaviour\Features\Context\Domain\AbstractDomainFeatureContext;

class HookModuleFeatureContext extends AbstractDomainFeatureContext
{
    private const NON_EXISTING_ID = 999999;

    /**
     * @Given the hook :hookName exists
     */
    public function theHookExists(string $hookName): void
    {
        $hookId = (int) Hook::getIdByName($hookName);
        Assert::assertGreaterThan(0, $hookId, sprintf('Hook "%s" does not exist.', $hookName));
    }

    /**
     * @Given the module :moduleName is not registered on any hook
     */
    public function theModuleIsNotRegisteredOnAnyHook(string $moduleName): void
    {
        $moduleId = (int) $this->getModule($moduleName)->id;

        Db::getInstance()->delete('hook_module', 'id_module = ' . $moduleId);
        Db::getInstance()->delete('hook_module_exceptions', 'id_module = ' . $moduleId);
    }

    /**
     * @Given the module :moduleName is hooked to :hookName
     */
    public function theModuleIsHookedTo(string $moduleName, string $hookName): void
    {
        $module = $this->getModule($moduleName);
        $this->getHookIdOrFail($hookName);

        $module->registerHook($hookName);
    }

    /**
     * @Given the module :moduleName is hooked to :hookName with exceptions :exceptions
     */
    public function theModuleIsHookedToWithExceptions(string $moduleName, string $hookName, string $exceptions): void
    {
        $this->theModuleIsHookedTo($moduleName, $hookName);

        $module = $this->getModule($moduleName);
        $hookId = $this->getHookIdOrFail($hookName);

        $module->registerExceptions($hookId, $this->parseExceptions($exceptions));
    }

    /**
     * @When I hook module :moduleName to hook :hookName with no exceptions
     */
    public function iHookModuleToHook(string $moduleName, string $hookName): void
    {
        $this->iHookModuleToHookWithExceptions($moduleName, $hookName, '');
    }

    /**
     * @When I hook module :moduleName to hook :hookName with exceptions :exceptions
     */
    public function iHookModuleToHookWithExceptions(string $moduleName, string $hookName, string $exceptions): void
    {
        $moduleId = (int) $this->getModule($moduleName)->id;
        $hookId = $this->getHookIdOrFail($hookName);

        try {
            $this->getCommandBus()->handle(new HookModuleCommand(
                $moduleId,
                $hookId,
                $this->parseExceptions($exceptions)
            ));
        } catch (HookException $e) {
            $this->setLastException($e);
        }
    }

    /**
     * @When I hook module :moduleName to a non-existing hook
     */
    public function iHookModuleToNonExistingHook(string $moduleName): void
    {
        $moduleId = (int) $this->getModule($moduleName)->id;

        try {
            $this->getCommandBus()->handle(new HookModuleCommand(
                $moduleId,
                self::NON_EXISTING_ID,
                []
            ));
        } catch (HookException $e) {
            $this->setLastException($e);
        }
    }

    /**
     * @When I hook a non-existing module to hook :hookName
     */
    public function iHookNonExistingModuleToHook(string $hookName): void
    {
        $hookId = $this->getHookIdOrFail($hookName);

        try {
            $this->getCommandBus()->handle(new HookModuleCommand(
                self::NON_EXISTING_ID,
                $hookId,
                []
            ));
        } catch (HookException $e) {
            $this->setLastException($e);
        }
    }

    /**
     * @When I edit module :moduleName on hook :hookName setting exceptions to :exceptions
     */
    public function iEditModuleExceptions(string $moduleName, string $hookName, string $exceptions): void
    {
        $moduleId = (int) $this->getModule($moduleName)->id;
        $hookId = $this->getHookIdOrFail($hookName);

        try {
            $this->getCommandBus()->handle(new EditHookedModuleCommand(
                $moduleId,
                $hookId,
                $hookId,
                $this->parseExceptions($exceptions)
            ));
        } catch (HookException $e) {
            $this->setLastException($e);
        }
    }

    /**
     * @When I edit module :moduleName moving it from hook :hookName to hook :newHookName with no exceptions
     */
    public function iMoveModuleBetweenHooks(string $moduleName, string $hookName, string $newHookName): void
    {
        $moduleId = (int) $this->getModule($moduleName)->id;
        $hookId = $this->getHookIdOrFail($hookName);
        $newHookId = $this->getHookIdOrFail($newHookName);

        try {
            $this->getCommandBus()->handle(new EditHookedModuleCommand(
                $moduleId,
                $hookId,
                $newHookId,
                []
            ));
        } catch (HookException $e) {
            $this->setLastException($e);
        }
    }

    /**
     * @Then the module :moduleName should be registered on hook :hookName
     */
    public function theModuleShouldBeRegisteredOnHook(string $moduleName, string $hookName): void
    {
        Assert::assertTrue(
            $this->isRegistered($moduleName, $hookName),
            sprintf('Module "%s" is not registered on hook "%s".', $moduleName, $hookName)
        );
    }

    /**
     * @Then the module :moduleName should not be registered on hook :hookName
     */
    public function theModuleShouldNotBeRegisteredOnHook(string $moduleName, string $hookName): void
    {
        Assert::assertFalse(
            $this->isRegistered($moduleName, $hookName),
            sprintf('Module "%s" is unexpectedly registered on hook "%s".', $moduleName, $hookName)
        );
    }

    /**
     * @Then the module :moduleName exceptions on hook :hookName should be :expectedExceptions
     */
    public function theModuleExceptionsShouldBe(string $moduleName, string $hookName, string $expectedExceptions): void
    {
        $moduleId = (int) $this->getModule($moduleName)->id;
        $hookId = $this->getHookIdOrFail($hookName);

        $rows = Db::getInstance()->executeS(
            'SELECT file_name FROM ' . _DB_PREFIX_ . 'hook_module_exceptions'
            . ' WHERE id_module = ' . $moduleId . ' AND id_hook = ' . $hookId
        );

        $actualExceptions = array_values(array_unique(array_map(
            static fn (array $row): string => (string) $row['file_name'],
            is_array($rows) ? $rows : []
        )));
        $expected = $this->parseExceptions($expectedExceptions);

        sort($actualExceptions);
        sort($expected);

        Assert::assertEquals(
            $expected,
            $actualExceptions,
            sprintf(
                'Expected exceptions [%s] but got [%s] for module "%s" on hook "%s".',
                implode(', ', $expected),
                implode(', ', $actualExceptions),
                $moduleName,
                $hookName
            )
        );
    }

    /**
     * @Then the list of possible hooks for module :moduleName should contain :hookName as registered
     */
    public function thePossibleHooksContainAsRegistered(string $moduleName, string $hookName): void
    {
        $info = $this->findPossibleHook($moduleName, $hookName);

        Assert::assertNotNull(
            $info,
            sprintf('Hook "%s" is not in the list of possible hooks for module "%s".', $hookName, $moduleName)
        );
        Assert::assertTrue(
            $info->isRegistered(),
            sprintf('Hook "%s" is in the list but not marked as registered for module "%s".', $hookName, $moduleName)
        );
    }

    /**
     * @Then the list of possible hooks for module :moduleName should contain :hookName as not registered
     */
    public function thePossibleHooksContainAsNotRegistered(string $moduleName, string $hookName): void
    {
        $info = $this->findPossibleHook($moduleName, $hookName);

        Assert::assertNotNull(
            $info,
            sprintf('Hook "%s" is not in the list of possible hooks for module "%s".', $hookName, $moduleName)
        );
        Assert::assertFalse(
            $info->isRegistered(),
            sprintf('Hook "%s" is in the list but marked as registered for module "%s".', $hookName, $moduleName)
        );
    }

    /**
     * @Then I should get an error that the module is already hooked
     */
    public function iShouldGetErrorModuleAlreadyHooked(): void
    {
        $this->assertLastErrorIs(ModuleAlreadyHookedException::class);
    }

    /**
     * @Then I should get an error that the module cannot be hooked
     */
    public function iShouldGetErrorModuleCannotBeHooked(): void
    {
        $this->assertLastErrorIs(ModuleCannotBeHookedException::class);
    }

    /**
     * @Then I should get an error that the hook was not found
     */
    public function iShouldGetErrorHookNotFound(): void
    {
        $this->assertLastErrorIs(HookNotFoundException::class);
    }

    /**
     * @Then I should get an error that the hook update failed
     */
    public function iShouldGetErrorCannotUpdateHook(): void
    {
        $this->assertLastErrorIs(CannotUpdateHookException::class);
    }

    private function getModule(string $moduleName): Module
    {
        $module = Module::getInstanceByName($moduleName);
        if (!$module) {
            throw new RuntimeException(sprintf('Module "%s" cannot be loaded.', $moduleName));
        }

        return $module;
    }

    private function getHookIdOrFail(string $hookName): int
    {
        $hookId = (int) Hook::getIdByName($hookName);
        if ($hookId <= 0) {
            throw new RuntimeException(sprintf('Hook "%s" was not found.', $hookName));
        }

        return $hookId;
    }

    private function isRegistered(string $moduleName, string $hookName): bool
    {
        $moduleId = (int) $this->getModule($moduleName)->id;
        $hookId = $this->getHookIdOrFail($hookName);

        $row = Db::getInstance()->getRow(
            'SELECT id_module FROM ' . _DB_PREFIX_ . 'hook_module'
            . ' WHERE id_module = ' . $moduleId . ' AND id_hook = ' . $hookId
        );

        return !empty($row);
    }

    /**
     * @return string[]
     */
    private function parseExceptions(string $exceptions): array
    {
        if (trim($exceptions) === '') {
            return [];
        }

        return array_values(array_unique(array_filter(
            array_map('trim', explode(',', $exceptions))
        )));
    }

    private function findPossibleHook(string $moduleName, string $hookName): ?HookableInfo
    {
        $moduleId = (int) $this->getModule($moduleName)->id;

        /** @var HookableInfo[] $hooks */
        $hooks = $this->getQueryBus()->handle(new GetPossibleHooksForModule($moduleId));

        foreach ($hooks as $hook) {
            if ($hook->getName() === $hookName) {
                return $hook;
            }
        }

        return null;
    }
}
