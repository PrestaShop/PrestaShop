<?php
/**
 * 2007-2019 PrestaShop and Contributors
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShopBundle\Form\Admin\Sell\Attachment;

use PrestaShopBundle\Form\Admin\Type\TranslatableType;
use PrestaShopBundle\Translation\TranslatorAwareTrait;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Class AttachmentType
 */
class AttachmentType extends AbstractType
{
    use TranslatorAwareTrait;

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TranslatableType::class, [
                'type' => TextType::class,
                'required' => true,
                'options' => [
                    'constraints' => [
                        new NotBlank([
                            'message' => $this->trans('This field cannot be empty', [], 'Admin.Notifications.Error'),
                        ]),
                    ],
                ]
            ])
            ->add('file_description', TranslatableType::class, [
                'type' => TextType::class,
                'required' => false,
            ])
            ->add('file', FileType::class, [
                'required' => false,
                'mapped' => true,
                'constraints' => [
                    new NotBlank([
                        'message' => $this->trans('This field cannot be empty', [], 'Admin.Notifications.Error'),
                        'groups' => ['validate']
                    ]),
                ],
            ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired(['is_edit_form', 'has_old_file']);
        $resolver->setDefaults([
            'validation_groups' => function (FormInterface $form) {
                $groups = ['Default'];
                $data = $form->getData();
                $options = $form->getConfig()->getOptions();

                $shouldValidateFile = (!$options['is_edit_form'] ||
                    (isset($options['has_old_file']) &&
                    !$options['has_old_file'])) &&
                    $data['file'] === null;

                if ($shouldValidateFile) {
                    array_push($groups, 'validate');
                }

                return $groups;
            },
            'has_old_file' => false,
        ]);

        $resolver->setAllowedTypes('has_old_file', 'bool');
        $resolver->setAllowedTypes('is_edit_form', 'bool');

    }
}
