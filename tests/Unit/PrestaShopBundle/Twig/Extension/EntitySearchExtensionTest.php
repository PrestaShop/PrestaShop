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
