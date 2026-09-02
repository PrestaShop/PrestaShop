<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Form\Admin\Sell\Attachment;

use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\CleanHtml;
use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\DefaultLanguage;
use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\TypedRegex;
use PrestaShop\PrestaShop\Core\Domain\Attachment\Configuration\AttachmentConstraint;
use PrestaShopBundle\Form\Admin\Type\TranslatableType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Length;

/**
 * Backwards compatibility break introduced in 1.7.8.0 due to extension of TranslationAwareType instead of using trait
 *
 * Attachment form type definition
 */
class AttachmentType extends TranslatorAwareType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $required = true;
        if (isset($options['data']['file_name']) && $options['data']['file_name']) {
            $required = false;
        }
        $builder
            ->add('name', TranslatableType::class, [
                'type' => TextType::class,
                'required' => true,
                'label' => $this->trans('File name', 'Admin.Global'),
                'options' => [
                    'constraints' => [
                        new TypedRegex(
                            [
                                'type' => 'generic_name',
                            ]
                        ),
                        new Length(
                            [
                                'max' => AttachmentConstraint::MAX_NAME_LENGTH,
                                'maxMessage' => $this->trans(
                                    'This field cannot be longer than %limit% characters',
                                    'Admin.Notifications.Error',
                                    ['%limit%' => AttachmentConstraint::MAX_NAME_LENGTH]
                                ),
                            ]
                        ),
                    ],
                ],
                'constraints' => [
                    new DefaultLanguage(),
                ],
            ])
            ->add('file_description', TranslatableType::class, [
                'type' => TextType::class,
                'required' => false,
                'label' => $this->trans('Description', 'Admin.Global'),
                'options' => [
                    'constraints' => [
                        new CleanHtml(),
                    ],
                ],
            ])
            ->add('file', FileType::class, [
                'required' => $required,
                'label' => $this->trans('File', 'Admin.Global'),
            ])
        ;
    }
}
