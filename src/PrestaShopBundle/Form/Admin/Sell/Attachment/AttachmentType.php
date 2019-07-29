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
            ->add('file_name', TextType::class, [
                'required' => true,
                'constraints' => [
                    new NotBlank([
                        'message' => $this->trans('This field cannot be empty', [], 'Admin.Notifications.Error'),
                    ]),
                ],
            ])
            ->add('description', TextType::class, [
                'required' => false,
            ])
            ->add('file', FileType::class, [
                'required' => false,
                'mapped' => true,
                'constraints' => [
                    new NotBlank([
                        'message' => $this->trans('This field cannot be empty', [], 'Admin.Notifications.Error'),
                        'groups' => ['create']
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
        $resolver->setDefaults([
            'validation_groups' => function (FormInterface $form) {
                $data = $form->getData();

                $groups = ['Default'];

                if (!isset($data['id']) || $data['id'] === null) {
                    array_push($groups, 'create');
                }

                return $groups;
            },
        ]);
    }
}
