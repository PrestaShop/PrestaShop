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

namespace Tests\Unit\Core\FeatureFlag;

use Generator;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\FeatureFlag\FeatureFlagManager;
use PrestaShop\PrestaShop\Core\FeatureFlag\Layer\DbLayer;
use PrestaShop\PrestaShop\Core\FeatureFlag\Layer\DotEnvLayer;
use PrestaShop\PrestaShop\Core\FeatureFlag\Layer\EnvLayer;
use PrestaShop\PrestaShop\Core\FeatureFlag\Layer\QueryLayer;
use PrestaShopBundle\Entity\FeatureFlag;
use PrestaShopBundle\Entity\Repository\FeatureFlagRepository;
use Psr\Container\ContainerInterface;

class FeatureFlagManagerTest extends TestCase
{
    /**
     * Test FeatureFlagManager with some cases.
     *
     * @dataProvider provideLayersTestsData
     */
    public function testManagerForDbFeatureFlag(array $testsData): void
    {
        $featureFlag = new FeatureFlag('test_ff');
        $featureFlag->setType('env,query,dotenv,db');

        $envLayer = $this->createLayerMock(EnvLayer::class, $testsData['layers']['env']);
        $queryLayer = $this->createLayerMock(QueryLayer::class, $testsData['layers']['query']);
        $dotenvLayer = $this->createLayerMock(DotEnvLayer::class, $testsData['layers']['dotenv']);
        $dbLayer = $this->createLayerMock(DbLayer::class, $testsData['layers']['db']);

        $featureFlagRepository = $this->createFeatureFlagRepositoryMock($featureFlag);
        $container = $this->createContainerInterfaceMock($envLayer, $queryLayer, $dotenvLayer, $dbLayer);
        $featureFlagManager = new FeatureFlagManager($container, $featureFlagRepository);

        $usedLayer = $featureFlagManager->getUsedType($featureFlag->getName());
        $this->assertEquals($testsData['layerUsed'], $usedLayer);

        $isReadonly = $featureFlagManager->isReadonly($featureFlag->getname());
        $this->assertEquals($testsData['isReadonly'], $isReadonly);

        $isEnabled = $featureFlagManager->isEnabled($featureFlag->getName());
        $this->assertEquals($testsData['isEnabled'], $isEnabled);

        $isDisabled = $featureFlagManager->isDisabled($featureFlag->getName());
        $this->assertEquals(!$testsData['isEnabled'], $isDisabled);

        $mockedLayerUsed = match ($testsData['layerUsed']) {
            'env' => $envLayer,
            'query' => $queryLayer,
            'dotenv' => $dotenvLayer,
            'db' => $dbLayer,
            default => null
        };

        $mockedLayerUsed->expects($this->once())
            ->method('enable')
            ->with($featureFlag->getName());
        $featureFlagManager->enable($featureFlag->getName());

        $mockedLayerUsed->expects($this->once())
            ->method('disable')
            ->with($featureFlag->getName());
        $featureFlagManager->disable($featureFlag->getName());
    }

    public function provideLayersTestsData(): Generator
    {
        yield 'EnvLayer must be in used & feature flag is enabled' => [[
            'layers' => [
                'env' => ['canBeUsed' => true, 'isEnabled' => true],
                'query' => ['canBeUsed' => true, 'isEnabled' => false],
                'dotenv' => ['canBeUsed' => true, 'isEnabled' => false],
                'db' => ['canBeUsed' => true, 'isEnabled' => false],
            ],
            'layerUsed' => 'env',
            'isReadonly' => true,
            'isEnabled' => true,
        ]];
        yield 'EnvLayer must be in used & feature flag is disabled' => [[
            'layers' => [
                'env' => ['canBeUsed' => true, 'isEnabled' => false],
                'query' => ['canBeUsed' => true, 'isEnabled' => true],
                'dotenv' => ['canBeUsed' => true, 'isEnabled' => true],
                'db' => ['canBeUsed' => true, 'isEnabled' => true],
            ],
            'layerUsed' => 'env',
            'isReadonly' => true,
            'isEnabled' => false,
        ]];

        yield 'QueryLayer must be in used & feature flag is enabled' => [[
            'layers' => [
                'env' => ['canBeUsed' => false, 'isEnabled' => false],
                'query' => ['canBeUsed' => true, 'isEnabled' => true],
                'dotenv' => ['canBeUsed' => true, 'isEnabled' => false],
                'db' => ['canBeUsed' => true, 'isEnabled' => false],
            ],
            'layerUsed' => 'query',
            'isReadonly' => true,
            'isEnabled' => true,
        ]];
        yield 'QueryLayer must be in used & feature flag is disabled' => [[
            'layers' => [
                'env' => ['canBeUsed' => false, 'isEnabled' => true],
                'query' => ['canBeUsed' => true, 'isEnabled' => false],
                'dotenv' => ['canBeUsed' => true, 'isEnabled' => true],
                'db' => ['canBeUsed' => true, 'isEnabled' => true],
            ],
            'layerUsed' => 'query',
            'isReadonly' => true,
            'isEnabled' => false,
        ]];

        yield 'DotEnvLayer must be in used & feature flag is enabled' => [[
            'layers' => [
                'env' => ['canBeUsed' => false, 'isEnabled' => false],
                'query' => ['canBeUsed' => false, 'isEnabled' => false],
                'dotenv' => ['canBeUsed' => true, 'isEnabled' => true],
                'db' => ['canBeUsed' => true, 'isEnabled' => false],
            ],
            'layerUsed' => 'dotenv',
            'isReadonly' => false,
            'isEnabled' => true,
        ]];
        yield 'DotEnvLayer must be in used & feature flag is disabled' => [[
            'layers' => [
                'env' => ['canBeUsed' => false, 'isEnabled' => true],
                'query' => ['canBeUsed' => false, 'isEnabled' => true],
                'dotenv' => ['canBeUsed' => true, 'isEnabled' => false],
                'db' => ['canBeUsed' => true, 'isEnabled' => true],
            ],
            'layerUsed' => 'dotenv',
            'isReadonly' => false,
            'isEnabled' => false,
        ]];

        yield 'DbLayer must be in used & feature flag is enabled' => [[
            'layers' => [
                'env' => ['canBeUsed' => false, 'isEnabled' => false],
                'query' => ['canBeUsed' => false, 'isEnabled' => false],
                'dotenv' => ['canBeUsed' => false, 'isEnabled' => false],
                'db' => ['canBeUsed' => true, 'isEnabled' => true],
            ],
            'layerUsed' => 'db',
            'isReadonly' => false,
            'isEnabled' => true,
        ]];
        yield 'DbLayer must be in used & feature flag is disabled' => [[
            'layers' => [
                'env' => ['canBeUsed' => false, 'isEnabled' => true],
                'query' => ['canBeUsed' => false, 'isEnabled' => true],
                'dotenv' => ['canBeUsed' => false, 'isEnabled' => true],
                'db' => ['canBeUsed' => true, 'isEnabled' => false],
            ],
            'layerUsed' => 'db',
            'isReadonly' => false,
            'isEnabled' => false,
        ]];
    }

    private function createContainerInterfaceMock($envLayer, $queryLayer, $dotenvLayer, $dbLayer)
    {
        $mock = $this->getMockBuilder(ContainerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $mock->expects($this->atLeastOnce())
            ->method('has')
            ->willReturnCallback(fn ($layer) => match ($layer) {
                'env' => true,
                'query' => true,
                'dotenv' => true,
                'db' => true,
                default => false
            });

        $mock->expects($this->atLeastOnce())
            ->method('get')
            ->willReturnCallback(fn ($layer) => match ($layer) {
                'env' => $envLayer,
                'query' => $queryLayer,
                'dotenv' => $dotenvLayer,
                'db' => $dbLayer,
                default => null
            });

        return $mock;
    }

    private function createFeatureFlagRepositoryMock(FeatureFlag $featureFlag)
    {
        $mock = $this->getMockBuilder(FeatureFlagRepository::class)
            ->disableOriginalConstructor()
            ->getMock();

        $mock->expects($this->any())
            ->method('getByName')
            ->will($this->returnValue($featureFlag));

        return $mock;
    }

    private function createLayerMock(string $className, array $testCase)
    {
        $mock = $this->getMockBuilder($className)
            ->onlyMethods(['canBeUsed', 'isEnabled', 'disable', 'enable'])
            ->disableOriginalConstructor()
            ->getMock();

        $mock->expects($this->any())
            ->method('canBeUsed')
            ->will($this->returnValue($testCase['canBeUsed']));

        $mock->expects($this->any())
            ->method('isEnabled')
            ->will($this->returnValue($testCase['isEnabled']));

        return $mock;
    }
}
