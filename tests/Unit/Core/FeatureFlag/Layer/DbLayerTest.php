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

namespace Tests\Unit\Core\FeatureFlag\Layer;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\FeatureFlag\Layer\DbLayer;
use PrestaShopBundle\Entity\FeatureFlag;
use PrestaShopBundle\Entity\Repository\FeatureFlagRepository;

class DbLayerTest extends TestCase
{
    private const FEATURE_FLAG_TEST = 'feature_flag_test';
    private $featureFlag;
    private $featureFlagRepository;

    public function __construct()
    {
        parent::__construct();

        $this->featureFlag = new FeatureFlag(self::FEATURE_FLAG_TEST);

        $this->featureFlagRepository = $this->getMockBuilder(FeatureFlagRepository::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->featureFlagRepository->expects($this->any())
            ->method('getByName')
            ->will($this->returnValue($this->featureFlag));
    }

    public function testIsReadonly()
    {
        $layer = new DbLayer($this->featureFlagRepository);
        $this->assertFalse($layer->isReadonly());
    }

    public function testGetTypeName()
    {
        $layer = new DbLayer($this->featureFlagRepository);
        $this->assertEquals('db', $layer->getTypeName());
    }

    public function testCanBeUsed()
    {
        $layer = new DbLayer($this->featureFlagRepository);
        $this->assertTrue($layer->canBeUsed($this->featureFlag->getName()));
    }

    public function testIsEnabled()
    {
        $layer = new DbLayer($this->featureFlagRepository);
        $this->featureFlagRepository->expects($this->once())
            ->method('isEnabled')
            ->willReturnCallback(fn ($featureFlagName) => match ($featureFlagName) {
                self::FEATURE_FLAG_TEST => true,
                default => false
            });
        $this->assertTrue($layer->isEnabled(self::FEATURE_FLAG_TEST));
    }

    public function testIsDisabled()
    {
        $layer = new DbLayer($this->featureFlagRepository);
        $this->featureFlagRepository->expects($this->once())
            ->method('isEnabled')
            ->willReturnCallback(fn ($featureFlagName) => match ($featureFlagName) {
                self::FEATURE_FLAG_TEST => false,
                default => true
            });
        $this->assertFalse($layer->isEnabled(self::FEATURE_FLAG_TEST));
    }

    public function testEnable()
    {
        $layer = new DbLayer($this->featureFlagRepository);
        $this->featureFlagRepository->expects($this->once())
            ->method('enable')
            ->with(self::FEATURE_FLAG_TEST);
        $layer->enable(self::FEATURE_FLAG_TEST);
    }

    public function testDisable()
    {
        $layer = new DbLayer($this->featureFlagRepository);
        $this->featureFlagRepository->expects($this->once())
            ->method('disable')
            ->with(self::FEATURE_FLAG_TEST);
        $layer->disable(self::FEATURE_FLAG_TEST);
    }
}
