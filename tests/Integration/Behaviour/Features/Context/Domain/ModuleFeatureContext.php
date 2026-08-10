<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Behaviour\Features\Context\Domain;

use Behat\Gherkin\Node\TableNode;
use Module;
use PHPUnit\Framework\Assert;
use PrestaShop\PrestaShop\Core\Domain\Module\Command\BulkToggleModuleStatusCommand;
use PrestaShop\PrestaShop\Core\Domain\Module\Command\BulkUninstallModuleCommand;
use PrestaShop\PrestaShop\Core\Domain\Module\Command\InstallModuleCommand;
use PrestaShop\PrestaShop\Core\Domain\Module\Command\ResetModuleCommand;
use PrestaShop\PrestaShop\Core\Domain\Module\Command\UninstallModuleCommand;
use PrestaShop\PrestaShop\Core\Domain\Module\Command\UpdateModuleStatusCommand;
use PrestaShop\PrestaShop\Core\Domain\Module\Command\UpgradeModuleCommand;
use PrestaShop\PrestaShop\Core\Domain\Module\Command\UploadModuleCommand;
use PrestaShop\PrestaShop\Core\Domain\Module\Exception\AlreadyInstalledModuleException;
use PrestaShop\PrestaShop\Core\Domain\Module\Exception\ModuleAlreadyUpToDateException;
use PrestaShop\PrestaShop\Core\Domain\Module\Exception\ModuleException;
use PrestaShop\PrestaShop\Core\Domain\Module\Exception\ModuleNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Module\Exception\ModuleNotInstalledException;
use PrestaShop\PrestaShop\Core\Domain\Module\Query\GetModuleInfos;
use PrestaShop\PrestaShop\Core\Domain\Module\QueryResult\ModuleInfos;
use PrestaShop\PrestaShop\Core\Module\OverriddenModulesProvider;
use RuntimeException;
use Tests\Integration\Behaviour\Features\Context\CommonFeatureContext;
use Tests\Integration\Behaviour\Features\Context\Util\PrimitiveUtils;

class ModuleFeatureContext extends AbstractDomainFeatureContext
{
    /**
     * @var string[] Override files created by the scenario, removed once it ends
     */
    private array $createdOverrideFiles = [];

    /**
     * @Given module :technicalName is overridden by file :overrideFile
     *
     * The file only has to exist for the module to be reported as overridden, its content is
     * irrelevant, so a bare php file is enough and cannot interfere with the rest of the scenario.
     */
    public function overrideModule(string $technicalName, string $overrideFile): void
    {
        $overriddenFilePath = _PS_OVERRIDE_DIR_ . 'modules/' . $technicalName . '/' . $overrideFile;
        $overriddenFileDir = dirname($overriddenFilePath);

        // The scenario writes in the shop override folder, which may hold real overrides on a
        // developer machine: never overwrite a file the scenario did not create, as the clean up
        // would then delete it for good
        if (file_exists($overriddenFilePath) && !in_array($overriddenFilePath, $this->createdOverrideFiles, true)) {
            throw new RuntimeException(sprintf(
                'Cannot override module %s for the test, %s already exists',
                $technicalName,
                $overriddenFilePath
            ));
        }

        if (!is_dir($overriddenFileDir)) {
            mkdir($overriddenFileDir, 0777, true);
        }
        file_put_contents($overriddenFilePath, '<?php' . PHP_EOL);

        $this->createdOverrideFiles[] = $overriddenFilePath;
        $this->resetOverriddenModulesProvider();
    }

    /**
     * Override files live outside the module folder, so they are removed by hand once the scenario
     * is over to leave the shop as it was found.
     *
     * @AfterScenario
     */
    public function cleanUpOverrideFiles(): void
    {
        foreach ($this->createdOverrideFiles as $overriddenFilePath) {
            if (is_file($overriddenFilePath)) {
                unlink($overriddenFilePath);
            }

            // Remove the folders created for that file, as long as they are left empty
            $directory = dirname($overriddenFilePath);
            while (str_starts_with($directory, _PS_OVERRIDE_DIR_ . 'modules') && $this->isEmptyDirectory($directory)) {
                rmdir($directory);
                $directory = dirname($directory);
            }
        }

        $this->createdOverrideFiles = [];
        $this->resetOverriddenModulesProvider();
    }

    private function isEmptyDirectory(string $directory): bool
    {
        return is_dir($directory) && [] === array_diff(scandir($directory) ?: [], ['.', '..']);
    }

    /**
     * The provider only scans the override folder once, so it has to be told the folder changed.
     * In production this is not needed: the scan happens once per request.
     */
    private function resetOverriddenModulesProvider(): void
    {
        CommonFeatureContext::getContainer()->get(OverriddenModulesProvider::class)->reset();
    }

    /**
     * @Given module :technicalName has following infos:
     */
    public function assertModuleInfos(string $technicalName, TableNode $tableNode): void
    {
        try {
            /** @var ModuleInfos $moduleInfos */
            $moduleInfos = $this->getQueryBus()->handle(new GetModuleInfos($technicalName));
            $this->assertModuleInfosWithData($moduleInfos, $tableNode);
        } catch (ModuleException $e) {
            $this->setLastException($e);
        }
    }

    /**
     * @Then I should have an exception that module is not found
     */
    public function assertModuleNotFound(): void
    {
        $this->assertLastErrorIs(ModuleNotFoundException::class);
    }

    /**
     * @Then I should have an exception that module is already installed
     */
    public function assertModuleIsAlreadyInstalled(): void
    {
        $this->assertLastErrorIs(AlreadyInstalledModuleException::class);
    }

    /**
     * @Then I should have an exception that module is not installed
     */
    public function assertModuleNotInstalled(): void
    {
        $this->assertLastErrorIs(ModuleNotInstalledException::class);
    }

    /**
     * @Then I should have an exception that module is already up to date
     */
    public function assertModuleAlreadyUpToDate(): void
    {
        $this->assertLastErrorIs(ModuleAlreadyUpToDateException::class);
    }

    /**
     * @When /^I bulk (enable|disable) modules: "(.+)"$/
     */
    public function bulkToggleStatus(string $action, string $modulesRef): void
    {
        $modules = [];
        foreach (PrimitiveUtils::castStringArrayIntoArray($modulesRef) as $modulesReference) {
            $modules[] = $modulesReference;
        }

        try {
            $this->getCommandBus()->handle(new BulkToggleModuleStatusCommand(
                $modules,
                'enable' === $action
            ));
        } catch (ModuleException $e) {
            $this->setLastException($e);
        }

        // Clean the cache
        Module::resetStaticCache();
    }

    /**
     * @When /^I (enable|disable) module "(.+)"$/
     */
    public function updateModuleStatus(string $action, string $technicalName): void
    {
        try {
            $this->getCommandBus()->handle(new UpdateModuleStatusCommand(
                $technicalName,
                $action === 'enable'
            ));
        } catch (ModuleException $e) {
            $this->setLastException($e);
        }

        // Clean the cache
        Module::resetStaticCache();
    }

    /**
     * @When /^I uninstall module "(.+)" with deleteFiles (true|false)$/
     */
    public function uninstallModule(string $module, string $deleteFile): void
    {
        try {
            $this->getCommandBus()->handle(new UninstallModuleCommand($module, $deleteFile == 'true'));
        } catch (ModuleException $e) {
            $this->setLastException($e);
        }

        // Clean the cache
        Module::resetStaticCache();
    }

    /**
     * @When /^I bulk uninstall modules: "(.+)" with deleteFiles (true|false)$/
     */
    public function bulkUninstallModule(string $modulesRef, string $deleteFile): void
    {
        try {
            $modules = [];
            foreach (PrimitiveUtils::castStringArrayIntoArray($modulesRef) as $modulesReference) {
                $modules[] = $modulesReference;
            }

            $this->getCommandBus()->handle(new BulkUninstallModuleCommand($modules, $deleteFile == 'true'));
        } catch (ModuleException $e) {
            $this->setLastException($e);
        }

        // Clean the cache
        Module::resetStaticCache();
    }

    /**
     * @When I reset module :technicalName
     */
    public function resetModule(string $technicalName): void
    {
        try {
            $this->getCommandBus()->handle(new ResetModuleCommand(
                $technicalName,
                false
            ));
        } catch (ModuleException $e) {
            $this->setLastException($e);
        }

        // Clean the cache
        Module::resetStaticCache();
    }

    /**
     * @When I install module :technicalName
     */
    public function installModule(string $technicalName): void
    {
        try {
            $this->getCommandBus()->handle(new InstallModuleCommand($technicalName));
        } catch (ModuleException $e) {
            $this->setLastException($e);
        }
        // Clean the cache
        Module::resetStaticCache();
    }

    /**
     * @When /^I upload module from "(zip|url)" "(.+)" that should have the following infos:$/
     */
    public function uploadModule(string $sourceType, string $sourceGiven, TableNode $tableNode): void
    {
        switch ($sourceType) {
            case 'zip':
                $source = _PS_MODULE_DIR_ . $sourceGiven;
                break;
            case 'url':
                $source = $sourceGiven;
                break;
            default:
                $source = null;
                break;
        }
        try {
            $moduleInfos = $this->getCommandBus()->handle(new UploadModuleCommand($source));
            $this->assertModuleInfosWithData($moduleInfos, $tableNode);
        } catch (ModuleException $e) {
            $this->setLastException($e);
        }

        // Clean the cache
        Module::resetStaticCache();
    }

    /**
     * @When I upgrade module :technicalName
     */
    public function upgradeModule(string $technicalName): void
    {
        try {
            $this->getCommandBus()->handle(new UpgradeModuleCommand($technicalName));
        } catch (ModuleException $e) {
            $this->setLastException($e);
        }
        // Clean the cache
        Module::resetStaticCache();
    }

    private function assertModuleInfosWithData(ModuleInfos $moduleInfos, TableNode $tableNode): void
    {
        $data = $tableNode->getRowsHash();
        if (isset($data['technical_name'])) {
            Assert::assertEquals($data['technical_name'], $moduleInfos->getTechnicalName(), 'Invalid technical name');
        }
        if (isset($data['installed_version'])) {
            Assert::assertEquals($data['installed_version'] ?: null, $moduleInfos->getInstalledVersion(), 'Invalid installed version');
        }
        if (isset($data['module_version'])) {
            Assert::assertEquals($data['module_version'], $moduleInfos->getModuleVersion(), 'Invalid module_version version');
        }
        if (isset($data['enabled'])) {
            Assert::assertEquals(PrimitiveUtils::castStringBooleanIntoBoolean($data['enabled']), $moduleInfos->isEnabled(), 'Invalid enabled value');
        }
        if (isset($data['installed'])) {
            Assert::assertEquals(PrimitiveUtils::castStringBooleanIntoBoolean($data['installed']), $moduleInfos->isInstalled(), 'Invalid installed value');
        }
        if (isset($data['overridden'])) {
            Assert::assertEquals(PrimitiveUtils::castStringBooleanIntoBoolean($data['overridden']), $moduleInfos->isOverridden(), 'Invalid overridden value');
        }
        if (isset($data['overridden_files'])) {
            $expectedFiles = '' === $data['overridden_files'] ? [] : array_map('trim', explode(',', $data['overridden_files']));
            Assert::assertEquals($expectedFiles, $moduleInfos->getOverriddenFiles(), 'Invalid overridden files');
        }
    }
}
