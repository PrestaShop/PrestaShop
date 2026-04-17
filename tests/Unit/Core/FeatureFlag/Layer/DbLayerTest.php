<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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

    public function __construct(string $name)
    {
        parent::__construct($name);

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
