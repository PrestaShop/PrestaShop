<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Form\Admin\Sell\Discount;

use PrestaShop\PrestaShop\Core\Form\FormChoiceProviderInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

class DiscountTypeChoiceType extends ChoiceType
{
    public function __construct(
        protected readonly TranslatorInterface $translator,
        protected readonly FormChoiceProviderInterface $choiceProvider,
    ) {
        parent::__construct();
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);
        $resolver->setDefaults([
            'label' => $this->translator->trans('Discount type', [], 'Admin.Catalog.Feature'),
            'choices' => $this->choiceProvider->getChoices(),
            'multiple' => false,
            'required' => false,
            'placeholder' => $this->translator->trans('All', [], 'Admin.Global'),
        ]);
    }
}
