<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Form\Admin\Sell\CustomerService;

use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\DefaultLanguage;
use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\TypedRegex;
use PrestaShop\PrestaShop\Core\Domain\OrderMessage\OrderMessageConstraint;
use PrestaShopBundle\Form\Admin\Type\TranslatableType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Length;

/**
 * Backwards compatibility break introduced in 1.7.8.0 due to extension of TranslationAwareType instead of using trait
 *
 * Builds add/edit form for order message
 */
class OrderMessageType extends TranslatorAwareType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TranslatableType::class, [
                'label' => $this->trans('Name', 'Admin.Global'),
                'options' => [
                    'constraints' => [
                        new TypedRegex([
                            'type' => 'generic_name',
                        ]),
                        new Length([
                            'max' => OrderMessageConstraint::MAX_NAME_LENGTH,
                        ]),
                    ],
                ],
                'constraints' => [
                    new DefaultLanguage(),
                ],
            ])
            ->add('message', TranslatableType::class, [
                'label' => $this->trans('Message', 'Admin.Global'),
                'type' => TextareaType::class,
                'options' => [
                    'constraints' => [
                        new TypedRegex([
                            'type' => 'message',
                        ]),
                        new Length([
                            'max' => OrderMessageConstraint::MAX_MESSAGE_LENGTH,
                        ]),
                    ],
                    'attr' => [
                        'rows' => 5,
                    ],
                ],
                'constraints' => [
                    new DefaultLanguage(),
                ],
            ])
        ;
    }
}
