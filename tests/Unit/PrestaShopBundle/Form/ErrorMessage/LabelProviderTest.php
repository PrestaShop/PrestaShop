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

namespace Tests\Unit\PrestaShopBundle\Form\ErrorMessage;

use PrestaShopBundle\Controller\Exception\FieldLabelNotFoundException;
use PrestaShopBundle\Form\Admin\Type\CommonAbstractType;
use PrestaShopBundle\Form\ErrorMessage\LabelProvider;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Test\TypeTestCase;

class LabelProviderTest extends TypeTestCase
{
    public function testGetLabel(): void
    {
        $form = $this->factory->create(TestFormType::class);

        $labelProvider = new LabelProvider();
        self::assertSame('Field', $labelProvider->getLabel($form, 'field'));
        self::assertSame('Some field name', $labelProvider->getLabel($form, 'other_field'));
    }
    public function testThrowsFieldNotFoundException(): void
    {
        $form = $this->factory->create(TestFormType::class);

        $labelProvider = new LabelProvider();
        $this->expectException(FieldLabelNotFoundException::class);
        $labelProvider->getLabel($form, 'non_existing_field');
    }
}

class TestFormType extends CommonAbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('field', TextType::class, [
                'label' => 'Field',
            ])
            ->add('other_field', TextType::class, [
                'label' => 'Some field name',
            ]);
    }
}
