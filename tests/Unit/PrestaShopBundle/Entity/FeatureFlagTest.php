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
