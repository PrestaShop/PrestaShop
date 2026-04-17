<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\PrestaShopBundle\Twig\Extension;

use Generator;
use PHPUnit\Framework\TestCase;
use PrestaShopBundle\Twig\Extension\EntitySearchExtension;
use Symfony\Component\Form\FormView;

class EntitySearchExtensionTest extends TestCase
{
    /**
     * @dataProvider getEntityFieldData
     *
     * @param FormView $form
     * @param string $fieldName
     * @param string $expectedValue
     */
    public function testGetEntityFieldValue(FormView $form, string $fieldName, string $expectedValue): void
    {
        $extension = new EntitySearchExtension();
        $fieldValue = $extension->getEntityField($form, $fieldName);
        $this->assertEquals($expectedValue, $fieldValue);
    }

    public function getEntityFieldData(): Generator
    {
        $formView = new FormView();
        $formView->vars['value']['name'] = 'Test';

        // Value present
        yield [
            $formView,
            'name',
            'Test',
        ];

        // Value not here automatic placeholder
        yield [
            $formView,
            'id',
            '__id__',
        ];

        $parentFormView = new FormView();
        $parentFormView->vars['prototype_mapping'] = [
            'id' => '__value__',
        ];
        $formViewWithParent = new FormView($parentFormView);
        $formViewWithParent->vars['value']['name'] = 'Test';

        yield [
            $formViewWithParent,
            'name',
            'Test',
        ];

        yield [
            $formViewWithParent,
            'id',
            '__value__',
        ];

        yield [
            $formViewWithParent,
            'image',
            '__image__',
        ];
    }
}
