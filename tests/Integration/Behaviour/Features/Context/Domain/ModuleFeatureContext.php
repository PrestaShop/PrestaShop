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
use PrestaShop\PrestaShop\Core\Domain\Module\Query\GetModuleInfos;
use PrestaShop\PrestaShop\Core\Domain\Module\QueryResult\ModuleInfos;
use Tests\Integration\Behaviour\Features\Context\Util\PrimitiveUtils;

class ModuleFeatureContext extends AbstractDomainFeatureContext
{
    /**
     * @Given module :moduleReference has following infos:
     */
    public function assertModuleInfos(string $moduleReference, TableNode $tableNode): void
    {
        /** @var ModuleInfos $moduleInfos */
        $moduleInfos = $this->getQueryBus()->handle(new GetModuleInfos($this->referenceToId($moduleReference)));

        $data = $tableNode->getRowsHash();
        Assert::assertEquals($data['technical_name'], $moduleInfos->getTechnicalName());
        Assert::assertEquals($data['version'], $moduleInfos->getVersion());
        Assert::assertEquals(PrimitiveUtils::castStringBooleanIntoBoolean($data['enabled']), $moduleInfos->isEnabled());
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
}
