<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\PrestaShopBundle\Entity;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use PrestaShopBundle\Entity\FeatureFlag;

class FeatureFlagTest extends TestCase
{
    public function testFeatureFlagRequiresNotEmptyName()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Feature flag name cannot be empty');

        $featureFlag = new FeatureFlag('');
    }

    public function testAssertFeatureFlagProperties()
    {
        $featureFlag = new FeatureFlag('prestashop_800');

        $featureFlag->setDescriptionWording('a_b_c');
        $featureFlag->setDescriptionDomain('A.B.C');
        $featureFlag->setLabelWording('a b c d');
        $featureFlag->setLabelDomain('A.B.L');

        $this->assertEquals('prestashop_800', $featureFlag->getName());
        $this->assertFalse($featureFlag->isEnabled());
        $this->assertEquals('a_b_c', $featureFlag->getDescriptionWording());
        $this->assertEquals('A.B.C', $featureFlag->getDescriptionDomain());
        $this->assertEquals('a b c d', $featureFlag->getLabelWording());
        $this->assertEquals('A.B.L', $featureFlag->getLabelDomain());
        $this->assertEquals('env,dotenv,db', $featureFlag->getType());
        $this->assertEquals(['env', 'dotenv', 'db'], $featureFlag->getOrderedTypes());
    }

    public function testToggleWorks()
    {
        $featureFlag = new FeatureFlag('prestashop_800');

        $this->assertFalse($featureFlag->isEnabled());
        $featureFlag->enable();
        $this->assertTrue($featureFlag->isEnabled());
        $featureFlag->disable();
        $this->assertFalse($featureFlag->isEnabled());
    }
}
