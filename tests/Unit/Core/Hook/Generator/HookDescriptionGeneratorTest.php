<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
