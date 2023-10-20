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

declare(strict_types=1);

namespace Tests\Unit\PrestaShopBundle\Form\Admin\Type\Material;

use PHPUnit\Framework\TestCase;
use PrestaShopBundle\Form\Admin\Type\Material\MaterialChoiceTableType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;

class MaterialChoiceTableTypeTest extends TestCase
{
    /**
     * @dataProvider providerBuildView
     */
    public function testBuildView(array $viewData, array $choices, bool $expectedReturn): void
    {
        $mockForm = $this
            ->getMockBuilder(FormInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $mockForm
            ->expects($this->once())
            ->method('getViewData')
            ->willReturn($viewData);

        $formView = new FormView();
        $materialChoiceTableForm = new MaterialChoiceTableType();

        $materialChoiceTableForm->buildView(
            $formView,
            $mockForm,
            [
                'choices' => $choices,
            ]
        );

        $this->assertEquals($expectedReturn, $formView->vars['isCheckSelectAll']);
    }

    public function providerBuildView(): array
    {
        return [
            [
                [1, 2, 3],
                [1, 2, 3],
                true,
            ],
            [
                [1, 2],
                [1, 2, 3],
                false,
            ],
        ];
    }
}
