<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\PrestaShopBundle\Form;

use Generator;
use PrestaShopBundle\Form\FormBuilderModifier;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\ResolvedFormTypeInterface;

/**
 * We use an integration test instead of pure unit because manually creating advanced forms is a complicated and
 * long process, and it would probably end up in mistakes in the test initialization process itself which would
 * reduce the overall quality of the test.
 */
class FormBuilderModifierTest extends AbstractFormTester
{
    /**
     * @dataProvider getAddAfterData
     *
     * @param string $targetFieldName
     * @param array $childOptions
     * @param array $expectedChildren
     */
    public function testAddAfter(string $targetFieldName, array $childOptions, array $expectedChildren)
    {
        $formBuilder = $this->createFormBuilder(AdvancedFormType::class);
        $modifier = new FormBuilderModifier();
        $modifier->addAfter(
            $formBuilder,
            $targetFieldName,
            $childOptions['child'],
            $childOptions['type'] ?? null,
            $childOptions['options'] ?? []
        );

        $builderChildren = $formBuilder->all();
        $this->assertEquals(count($expectedChildren), count($builderChildren));

        $builderIndex = 0;
        foreach (array_keys($builderChildren) as $childName) {
            $expectedChild = $expectedChildren[$builderIndex];
            $this->assertEquals($expectedChild, $childName);
            ++$builderIndex;
        }
    }

    /**
     * @dataProvider getAddAfterData
     *
     * @param string $targetFieldName
     * @param array $childOptions
     * @param array $expectedChildren
     */
    public function testAddAfterChildBuilder(string $targetFieldName, array $childOptions, array $expectedChildren)
    {
        $formBuilder = $this->createFormBuilder(AdvancedFormType::class);
        $child = $this->createNamedBuilder($childOptions['child'], $childOptions['type'], $childOptions['options'] ?? []);

        $modifier = new FormBuilderModifier();
        $modifier->addAfter(
            $formBuilder,
            $targetFieldName,
            $child
        );

        $builderChildren = $formBuilder->all();
        $this->assertEquals(count($expectedChildren), count($builderChildren));

        $builderIndex = 0;
        foreach (array_keys($builderChildren) as $childName) {
            $expectedChild = $expectedChildren[$builderIndex];
            $this->assertEquals($expectedChild, $childName);
            ++$builderIndex;
        }
    }

    public function getAddAfterData(): Generator
    {
        yield [
            'type',
            [
                'child' => 'test1',
                'type' => ChoiceType::class,
            ],
            ['type', 'test1', 'target'],
        ];

        yield [
            'target',
            [
                'child' => 'test2',
                'type' => ChoiceType::class,
            ],
            ['type', 'target', 'test2'],
        ];
    }

    /**
     * @dataProvider getAddBeforeData
     *
     * @param string $targetFieldName
     * @param array $childOptions
     * @param array $expectedChildren
     */
    public function testAddBefore(string $targetFieldName, array $childOptions, array $expectedChildren)
    {
        $formBuilder = $this->createFormBuilder(AdvancedFormType::class);
        $modifier = new FormBuilderModifier();
        $modifier->addBefore(
            $formBuilder,
            $targetFieldName,
            $childOptions['child'],
            $childOptions['type'] ?? null,
            $childOptions['options'] ?? []
        );

        $builderChildren = $formBuilder->all();
        $this->assertEquals(count($expectedChildren), count($builderChildren));

        $builderIndex = 0;
        foreach (array_keys($builderChildren) as $childName) {
            $expectedChild = $expectedChildren[$builderIndex];
            $this->assertEquals($expectedChild, $childName);
            ++$builderIndex;
        }
    }

    /**
     * @dataProvider getAddBeforeData
     *
     * @param string $targetFieldName
     * @param array $childOptions
     * @param array $expectedChildren
     */
    public function testAddBeforeChildBuilder(string $targetFieldName, array $childOptions, array $expectedChildren)
    {
        $formBuilder = $this->createFormBuilder(AdvancedFormType::class);
        $child = $this->createNamedBuilder($childOptions['child'], $childOptions['type'], $childOptions['options'] ?? []);

        $modifier = new FormBuilderModifier();
        $modifier->addBefore(
            $formBuilder,
            $targetFieldName,
            $child
        );

        $builderChildren = $formBuilder->all();
        $this->assertEquals(count($expectedChildren), count($builderChildren));

        $builderIndex = 0;
        foreach (array_keys($builderChildren) as $childName) {
            $expectedChild = $expectedChildren[$builderIndex];
            $this->assertEquals($expectedChild, $childName);
            ++$builderIndex;
        }
    }

    public function getAddBeforeData(): Generator
    {
        yield [
            'type',
            [
                'child' => 'test1',
                'type' => ChoiceType::class,
            ],
            ['test1', 'type', 'target'],
        ];

        yield [
            'target',
            [
                'child' => 'test2',
                'type' => ChoiceType::class,
            ],
            ['type', 'test2', 'target'],
        ];
    }

    public function testAddBeforeOptions(): void
    {
        $expectedChoices = ['choice1', 'choice2'];
        $formBuilder = $this->createFormBuilder(AdvancedFormType::class);
        $modifier = new FormBuilderModifier();
        $modifier->addBefore(
            $formBuilder,
            'type',
            'test',
            ChoiceType::class,
            [
                'choices' => $expectedChoices,
            ]
        );

        $childBuilder = $formBuilder->get('test');
        /** @var ResolvedFormTypeInterface $resolvedType */
        $resolvedType = $childBuilder->getType();
        $this->assertInstanceOf(ChoiceType::class, $resolvedType->getInnerType());
        $this->assertSame('test', $childBuilder->getName());
        $this->assertSame($expectedChoices, $childBuilder->getOptions()['choices']);
    }

    public function testAddAfterOptions(): void
    {
        $expectedChoices = ['choice1', 'choice2'];
        $formBuilder = $this->createFormBuilder(AdvancedFormType::class);
        $modifier = new FormBuilderModifier();
        $modifier->addAfter(
            $formBuilder,
            'type',
            'test',
            ChoiceType::class,
            [
                'choices' => $expectedChoices,
            ]
        );

        $childBuilder = $formBuilder->get('test');
        /** @var ResolvedFormTypeInterface $resolvedType */
        $resolvedType = $childBuilder->getType();
        $this->assertInstanceOf(ChoiceType::class, $resolvedType->getInnerType());
        $this->assertSame('test', $childBuilder->getName());
        $this->assertSame($expectedChoices, $childBuilder->getOptions()['choices']);
    }
}
