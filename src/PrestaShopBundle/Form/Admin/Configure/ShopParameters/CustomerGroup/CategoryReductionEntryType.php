<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\Form\Admin\Configure\ShopParameters\CustomerGroup;

use PrestaShopBundle\Form\Admin\Type\TextPreviewType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Range;

class CategoryReductionEntryType extends TranslatorAwareType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('id_category', HiddenType::class)
            ->add('name', TextPreviewType::class, [
                'label' => false,
                'disabled' => true,
            ])
            ->add('reduction', NumberType::class, [
                'label' => false,
                'scale' => 2,
                'attr' => [
                    'placeholder' => '0',
                    'suffix' => '%',
                ],
                'constraints' => [
                    new Range([
                        'min' => 0,
                        'max' => 100,
                        'notInRangeMessage' => $this->trans(
                            'The discount value is incorrect (must be a percentage).',
                            'Admin.Shopparameters.Notification'
                        ),
                    ]),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults([
            'label' => false,
        ]);
    }
}
