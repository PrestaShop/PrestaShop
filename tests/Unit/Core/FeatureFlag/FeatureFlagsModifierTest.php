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

namespace Tests\Unit\Core\FeatureFlag;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectRepository;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\FeatureFlag\FeatureFlagsModifier;
use PrestaShopBundle\Entity\FeatureFlag;
use Symfony\Component\Translation\TranslatorInterface;

class FeatureFlagsModifierTest extends TestCase
{
    public function testGetConfigurationReturnsExpectedStructure()
    {
        $featureFlags = [
            new FeatureFlag('product_page_v1'),
            (new FeatureFlag('product_page_v2'))->enable(),
            new FeatureFlag('product_page_v3'),
        ];

        list($entityManagerMock, $repositoryMock) = $this->buildDoctrineServicesMocksForFetchAll($featureFlags);
        $translatorMock = $this->getMockBuilder(TranslatorInterface::class)->getMock();

        $modifier = new FeatureFlagsModifier($entityManagerMock, $translatorMock);

        $expected = [
            'product_page_v1' => false,
            'product_page_v2' => true,
            'product_page_v3' => false,
        ];

        $this->assertSame($expected, $modifier->getConfiguration());
    }

    public function testGetConfigurationReturnsEmptyIfNoFeatureFlagsAvailable()
    {
        list($entityManagerMock, $repositoryMock) = $this->buildDoctrineServicesMocksForFetchAll([]);
        $translatorMock = $this->getMockBuilder(TranslatorInterface::class)->getMock();

        $modifier = new FeatureFlagsModifier($entityManagerMock, $translatorMock);

        $this->assertEquals([], $modifier->getConfiguration());
    }

    public function testUpdateConfigurationIsSuccessfullWithValidPayload()
    {
        $featureFlags = [
            new FeatureFlag('product_page_v1'),
            new FeatureFlag('product_page_v2'),
            (new FeatureFlag('product_page_v3'))->enable(),
        ];

        list($entityManagerMock, $repositoryMock) = $this->buildDoctrineServicesMocksForFetchByName($featureFlags);
        $translatorMock = $this->getMockBuilder(TranslatorInterface::class)->getMock();

        $modifier = new FeatureFlagsModifier($entityManagerMock, $translatorMock);

        $payload = [
            'product_page_v1' => false,
            'product_page_v2' => true,
            'product_page_v3' => false,
        ];

        $modifier->updateConfiguration($payload);

        $this->assertFalse($featureFlags[0]->isEnabled());
        $this->assertTrue($featureFlags[1]->isEnabled());
        $this->assertFalse($featureFlags[2]->isEnabled());
    }

    public function testUpdateConfigurationWithEmptyPayload()
    {
        $featureFlags = [
            new FeatureFlag('product_page_v1'),
            new FeatureFlag('product_page_v2'),
            (new FeatureFlag('product_page_v3'))->enable(),
        ];

        list($entityManagerMock, $repositoryMock) = $this->buildDoctrineServicesMocksForFetchByName($featureFlags);
        $translatorMock = $this->getMockBuilder(TranslatorInterface::class)->getMock();

        $modifier = new FeatureFlagsModifier($entityManagerMock, $translatorMock);

        $modifier->updateConfiguration([]);

        $this->assertFalse($featureFlags[0]->isEnabled());
        $this->assertFalse($featureFlags[1]->isEnabled());
        $this->assertTrue($featureFlags[2]->isEnabled());
    }

    public function testUpdateConfigurationWithBadlyTypedData()
    {
        $featureFlags = [
            new FeatureFlag('product_page_v1'),
        ];

        list($entityManagerMock, $repositoryMock) = $this->buildDoctrineServicesMocksForFetchByName($featureFlags);
        $translatorMock = $this->getMockBuilder(TranslatorInterface::class)->getMock();

        $modifier = new FeatureFlagsModifier($entityManagerMock, $translatorMock);

        $payload = [
            1 => 'a',
        ];

        $this->expectException(InvalidArgumentException::class);

        $modifier->updateConfiguration($payload);
    }

    public function testUpdateConfigurationWithFeatureFlagsThatDoNotExist()
    {
        $featureFlags = [
            new FeatureFlag('product_page_v999'),
        ];

        list($entityManagerMock, $repositoryMock) = $this->buildDoctrineServicesMocksForFetchByName($featureFlags);
        $translatorMock = $this->getMockBuilder(TranslatorInterface::class)->getMock();

        $modifier = new FeatureFlagsModifier($entityManagerMock, $translatorMock);

        $payload = [
            'product_page_v1' => false,
        ];

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid feature flag configuration submitted, flag product_page_v1 does not exist');

        $modifier->updateConfiguration($payload);
    }

    public function testValidateConfigurationWhenPayloadIsValid()
    {
        $featureFlags = [
            new FeatureFlag('product_page_v1'),
            new FeatureFlag('product_page_v2'),
            (new FeatureFlag('product_page_v3'))->enable(),
        ];

        list($entityManagerMock, $repositoryMock) = $this->buildDoctrineServicesMocksForFetchByName($featureFlags);
        $translatorMock = $this->getMockBuilder(TranslatorInterface::class)->getMock();

        $modifier = new FeatureFlagsModifier($entityManagerMock, $translatorMock);

        $payload = [
            'product_page_v1' => false,
            'product_page_v2' => true,
            'product_page_v3' => false,
        ];

        $this->assertTrue($modifier->validateConfiguration($payload));
    }

    public function testValidateConfigurationWithBadlyTypedData()
    {
        $featureFlags = [
            new FeatureFlag('product_page_v1'),
        ];

        list($entityManagerMock, $repositoryMock) = $this->buildDoctrineServicesMocksForFetchByName($featureFlags);
        $translatorMock = $this->getMockBuilder(TranslatorInterface::class)->getMock();

        $modifier = new FeatureFlagsModifier($entityManagerMock, $translatorMock);

        $payload = [
            1 => 'a',
        ];

        $this->assertFalse($modifier->validateConfiguration($payload));
    }

    /**
     * @param array $fetchedFlags
     *
     * @return array
     */
    protected function buildDoctrineServicesMocksForFetchAll(array $fetchedFlags): array
    {
        $entityManagerMock = $this->getMockBuilder(EntityManagerInterface::class)->getMock();
        $repositoryMock = $this->getMockBuilder(ObjectRepository::class)->getMock();

        $entityManagerMock
            ->method('getRepository')
            ->willReturn($repositoryMock);

        $repositoryMock
            ->method('findAll')
            ->willReturn($fetchedFlags);

        return [$entityManagerMock, $repositoryMock];
    }

    /**
     * @param array $fetchedFlags
     *
     * @return array
     */
    protected function buildDoctrineServicesMocksForFetchByName(array $fetchedFlags): array
    {
        $entityManagerMock = $this->getMockBuilder(EntityManagerInterface::class)->getMock();
        $repositoryMock = $this->getMockBuilder(ObjectRepository::class)->getMock();

        $returnMap = array_map(function ($featureFlag) {
            return [['name' => $featureFlag->getName()], $featureFlag];
        }, $fetchedFlags);

        $entityManagerMock
            ->method('getRepository')
            ->willReturn($repositoryMock);

        $repositoryMock
            ->method('findOneBy')
            ->will($this->returnValueMap($returnMap));

        return [$entityManagerMock, $repositoryMock];
    }
}
