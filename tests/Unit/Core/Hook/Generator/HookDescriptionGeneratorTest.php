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
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace Tests\Unit\Core\Hook\Generator;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Hook\Generator\HookDescriptionGenerator;
use PrestaShop\PrestaShop\Core\Hook\HookDescription;
use PrestaShop\PrestaShop\Core\Util\String\StringModifierInterface;
use PrestaShop\PrestaShop\Core\Util\String\StringValidatorInterface;

class HookDescriptionGeneratorTest extends TestCase
{
    /**
     * @var MockObject|StringValidatorInterface
     */
    private $stringValidatorMock;

    /**
     * @var MockObject|StringModifierInterface
     */
    private $stringModifierMock;

    protected function setUp(): void
    {
        parent::setUp();

        $this->stringValidatorMock = $this
            ->getMockBuilder(StringValidatorInterface::class)
            ->getMock()
        ;

        $this->stringModifierMock = $this
            ->getMockBuilder(StringModifierInterface::class)
            ->getMock()
        ;
    }

    public function testItGetsExpectedDescriptionWithoutPlaceholder()
    {
        $expectedTitle = 'General description title';
        $expectedDescription = 'General description';

        $hookDescriptionList = [
            [
                'prefix' => 'action',
                'suffix' => 'GridQueryBuilderModifier',
                'title' => $expectedTitle,
                'description' => $expectedDescription,
            ],
        ];

        $this->setIsFoundBySuffixAndPrefixCondition();

        $descriptiveContentGenerator = new HookDescriptionGenerator(
            $hookDescriptionList,
            $this->stringValidatorMock,
            $this->stringModifierMock
        );

        $actual = $descriptiveContentGenerator->generate('actionCurrencyGridQueryBuilderModifier');

        $expected = new HookDescription(
            'actionCurrencyGridQueryBuilderModifier',
            $expectedTitle,
            $expectedDescription
        );

        $this->assertEquals($expected, $actual);
    }

    public function testItGetsExpectedDescription()
    {
        $hookId = 'currency';
        $expectedTitle = 'Currency Modify grid query builder';
        $expectedDescription = 'This hook allows to alter Doctrine query builder for currency grid';

        $hookDescriptionList = [
            [
                'prefix' => 'action',
                'suffix' => 'GridQueryBuilderModifier',
                'title' => '%s Modify grid query builder',
                'description' => 'This hook allows to alter Doctrine query builder for %s grid',
            ],
        ];

        $this->setIsFoundBySuffixAndPrefixCondition();

        $this->stringModifierMock
            ->method('splitByCamelCase')
            ->willReturn($hookId)
        ;

        $descriptiveContentGenerator = new HookDescriptionGenerator(
            $hookDescriptionList,
            $this->stringValidatorMock,
            $this->stringModifierMock
        );

        $actual = $descriptiveContentGenerator->generate('action' . $hookId . 'GridQueryBuilderModifier');

        $expected = new HookDescription(
            'action' . $hookId . 'GridQueryBuilderModifier',
            $expectedTitle,
            $expectedDescription
        );

        $this->assertEquals($expected, $actual);
    }

    private function setIsFoundBySuffixAndPrefixCondition()
    {
        $this->stringValidatorMock
            ->method('startsWithAndEndsWith')
            ->willReturn(true)
        ;

        $this->stringValidatorMock
            ->method('doesContainsWhiteSpaces')
            ->willReturn(false)
        ;
    }
}
