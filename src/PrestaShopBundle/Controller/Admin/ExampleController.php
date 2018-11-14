<?php
/**
 * 2007-2018 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
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
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShopBundle\Controller\Admin;

use PrestaShopBundle\Form\Admin\Type\SwitchType;
use PrestaShopBundle\Form\Admin\Type\TranslatableType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints\NotBlank;

class ExampleController extends Controller
{
    public function index(Request $request)
    {
        $form = $this->getForm();
        $form->handleRequest($request);

        return $this->render('@PrestaShop/Admin/example.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    private function getForm()
    {
        return $this->createFormBuilder(null, ['csrf_protection' => false,])
            ->add('text', TextType::class, [
                'label' => 'You can enter anything you want',
                'required' => false,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Yoooo, you cannot leave text blank!',
                    ])
                ]
            ])
            ->add('translatable_text', TranslatableType::class, [
                'required' => true,
                'label' => 'Enter me in any language',
            ])
            ->add('translatable_textarea', TranslatableType::class, [
                'type' => TextareaType::class,
                'label' => 'Enter me in any language again',
            ])
            ->add('switch', SwitchType::class, [
                'label' => 'Go ahead and switch the box',
                'data' => 1,
            ])
            ->add('select', ChoiceType::class, [
                'multiple' => false,
                'expanded' => false,
                'choices' => [
                    'Please select me' => 1,
                    'Don not select me' => 2,
                ],
                'label' => 'Best selection',
            ])
            ->add('textarea', TextareaType::class, [
                'attr' => [
                    'rows' => 5,
                ],
                'label' => 'Write a letter',
            ])
            ->add('checkboxes', ChoiceType::class, [
                'multiple' => true,
                'expanded' => true,
                'choices' => [
                    'One Material' => 1,
                    'Two Material' => 2,
                ],
                'label' => 'Choose your material',
            ])
            ->add('radios', ChoiceType::class, [
                'multiple' => false,
                'expanded' => true,
                'choices' => [
                    'Rounded' => 1,
                    'And rounded' => 2,
                ],
                'label' => 'Choose your circle',
            ])
            ->add('multiple_select', ChoiceType::class, [
                'multiple' => true,
                'expanded' => false,
                'choices' => [
                    'My thing' => 1,
                    'Your thing' => 2,
                ],
                'label' => 'Select things here',
            ])
            ->getForm()
        ;
    }
}
