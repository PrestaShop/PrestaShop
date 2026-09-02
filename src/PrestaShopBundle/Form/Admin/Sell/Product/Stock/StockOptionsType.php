<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\Form\Admin\Sell\Product\Stock;

use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Type;

class StockOptionsType extends TranslatorAwareType
{
    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('stock_location', TextType::class, [
                'label' => $this->trans('Stock location', 'Admin.Catalog.Feature'),
                'required' => false,
                'attr' => [
                    'placeholder' => $this->trans('Enter stock location', 'Admin.Catalog.Feature'),
                    'class' => 'medium-input',
                ],
                'modify_all_shops' => true,
            ])
            ->add('low_stock_threshold', NumberType::class, [
                'label' => $this->trans('Receive a low stock alert by email', 'Admin.Catalog.Feature'),
                'label_help_box' => $this->trans(
                    'The email will be sent to all users who have access to the Stock page. To modify permissions, go to Advanced Parameters > Team.',
                    'Admin.Catalog.Help',
                ),
                'constraints' => [
                    new Type(['type' => 'numeric']),
                ],
                'required' => false,
                // These two options allow to have a default data equals to zero but displayed as empty string
                'default_empty_data' => 0,
                'empty_view_data' => null,
                'modify_all_shops' => true,
                // @todo: need to trigger opening allShopscheckbox on "disabling_switch" change too.
                'disabling_switch' => true,
                'html5' => true,
                'attr' => [
                    'placeholder' => $this->trans('Enter threshold value', 'Admin.Catalog.Feature'),
                    'class' => 'small-input',
                ],
            ])
        ;
    }

    /**
     * {@inheritDoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);
        $resolver->setDefaults([
            'required' => false,
            'label' => false,
        ]);
    }
}
