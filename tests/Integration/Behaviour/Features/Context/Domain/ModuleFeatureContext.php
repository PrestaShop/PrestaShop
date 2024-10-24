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

namespace Tests\Integration\Behaviour\Features\Context\Domain;

use Behat\Gherkin\Node\TableNode;
use Module;
use PHPUnit\Framework\Assert;
use PrestaShop\PrestaShop\Core\Domain\Module\Command\BulkToggleModuleStatusCommand;
use PrestaShop\PrestaShop\Core\Domain\Module\Command\UninstallModuleCommand;
use PrestaShop\PrestaShop\Core\Domain\Module\Command\BulkUninstallModuleCommand;
use PrestaShop\PrestaShop\Core\Domain\Module\Command\ResetModuleCommand;
use PrestaShop\PrestaShop\Core\Domain\Module\Command\InstallModuleCommand;
use PrestaShop\PrestaShop\Core\Domain\Module\Command\UpdateModuleStatusCommand;
use PrestaShop\PrestaShop\Core\Domain\Module\Exception\CannotResetModuleException;
use PrestaShop\PrestaShop\Core\Domain\Module\Exception\AlreadyInstalledModuleException;
use PrestaShop\PrestaShop\Core\Domain\Module\Exception\ModuleNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Module\Exception\ModuleNotInstalledException;
use PrestaShop\PrestaShop\Core\Domain\Module\Query\GetModuleInfos;
use PrestaShop\PrestaShop\Core\Domain\Module\QueryResult\ModuleInfos;
use Tests\Integration\Behaviour\Features\Context\Util\PrimitiveUtils;

class ModuleFeatureContext extends AbstractDomainFeatureContext
{
    /**
     * @Given module :technicalName has following infos:
     */
    public function assertModuleInfos(string $technicalName, TableNode $tableNode): void
    {
        try {
            /** @var ModuleInfos $moduleInfos */
            $moduleInfos = $this->getQueryBus()->handle(new GetModuleInfos($technicalName));

            $data = $tableNode->getRowsHash();
            if (isset($data['technical_name'])) {
                Assert::assertEquals($data['technical_name'], $moduleInfos->getTechnicalName());
            }
            if (isset($data['version'])) {
                Assert::assertEquals($data['version'], $moduleInfos->getVersion());
            }
            if (isset($data['enabled'])) {
                Assert::assertEquals(PrimitiveUtils::castStringBooleanIntoBoolean($data['enabled']), $moduleInfos->isEnabled(), "invalid enabled value");
            }
            if (isset($data['installed'])) {
                Assert::assertEquals(PrimitiveUtils::castStringBooleanIntoBoolean($data['installed']), $moduleInfos->isInstalled(), "invalid installed value");
            }
        } catch (ModuleNotFoundException $e) {
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
     * @Then I should have an exception that disabled module cannot be reset
     */
    public function assertDisabledError(): void
    {
        $this->assertLastErrorIs(CannotResetModuleException::class, CannotResetModuleException::NOT_ACTIVE);
    }


    /**
     * @Then I should have an exception that module is not installed
     */
    public function assertModuleNotInstalled(): void
    {
        $this->assertLastErrorIs(ModuleNotInstalledException::class);
    }

    /**
     * @Then I should have an exception that module is already installed
     */
    public function assertModuleAlreadyInstalled(): void
    {
        $this->assertLastErrorIs(AlreadyInstalledModuleException::class);
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

        $this->getQueryBus()->handle(new BulkToggleModuleStatusCommand(
            $modules,
            'enable' === $action
        ));

        // Clean the cache
        Module::resetStaticCache();
    }

    /**
     * @When /^I (enable|disable) module "(.+)"$/
     */
    public function updateModuleStatus(string $action, string $technicalName): void
    {
        $this->getCommandBus()->handle(new UpdateModuleStatusCommand(
            $technicalName,
            $action === 'enable'
        ));

        // Clean the cache
        Module::resetStaticCache();
    }

    /**
    * @When /^I uninstall module "(.+)" with deleteFile (true|false)$/
    */
    public function uninstallModule(string $module, string $deleteFile): void
    {

        $this->getQueryBus()->handle(new UninstallModuleCommand($module, $deleteFile == "true"));

        // Clean the cache
        Module::resetStaticCache();
    }

    /**
    * @When /^I bulk uninstall modules: "(.+)" with deleteFile (true|false)$/
    */
   public function bulkUninstallModule(string $modulesRef, string $deleteFile): void
   {
       $modules = [];
       foreach (PrimitiveUtils::castStringArrayIntoArray($modulesRef) as $modulesReference) {
           $modules[] = $modulesReference;
       }

       $this->getQueryBus()->handle(new BulkUninstallModuleCommand($modules, $deleteFile == "true"));

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
        } catch (CannotResetModuleException $e) {
            $this->setLastException($e);
        }

        // Clean the cache
        Module::resetStaticCache();
    }

   /**
    * @When I install module :technicalName from "folder"
    */
    public function installModuleFromFolder(string $technicalName): void
    {
        try{
            $this->getQueryBus()->handle(new InstallModuleCommand($technicalName));
        } catch (AlreadyInstalledModuleException $e) {
            $this->setLastException($e);
        }
        // Clean the cache
        Module::resetStaticCache();
    }

   /**
    * @When /^I install module "(.+)" from "(zip|url)" "(.+)"$/
    */
    public function installModule(string $technicalName, string $sourceType, string $sourceGiven): void
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
        try{
            $this->getQueryBus()->handle(new InstallModuleCommand($technicalName, $source));
        } catch (ModuleNotFoundException $e) {
            $this->setLastException($e);
        }

        // Clean the cache
        Module::resetStaticCache();
    }
}
