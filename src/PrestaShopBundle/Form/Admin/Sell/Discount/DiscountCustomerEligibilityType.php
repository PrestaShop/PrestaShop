<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Form\Admin\Sell\Discount;

use PrestaShopBundle\Form\Admin\Type\CardType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DiscountCustomerEligibilityType extends TranslatorAwareType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('eligibility', DiscountCustomerEligibilityChoiceType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);
        $resolver->setDefaults([
            'label' => $this->trans('Select customer eligibility', 'Admin.Catalog.Feature'),
            'required' => false,
        ]);
    }

    public function getParent()
    {
        return CardType::class;
    }
}
