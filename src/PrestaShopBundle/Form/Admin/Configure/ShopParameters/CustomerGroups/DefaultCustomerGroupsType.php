<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\Form\Admin\Configure\ShopParameters\CustomerGroups;

use PrestaShop\PrestaShop\Core\Form\FormChoiceProviderInterface;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * "Default groups options" block of the "Shop Parameters > Customer Settings > Groups" page.
 */
final class DefaultCustomerGroupsType extends TranslatorAwareType
{
    public function __construct(
        TranslatorInterface $translator,
        array $locales,
        private readonly FormChoiceProviderInterface $customerGroupChoiceProvider,
    ) {
        parent::__construct($translator, $locales);
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $choices = $this->customerGroupChoiceProvider->getChoices();

        $builder
            ->add('visitors_group', ChoiceType::class, [
                'choices' => $choices,
                'label' => $this->trans('Visitors group', 'Admin.Shopparameters.Feature'),
                'help' => $this->trans('The group defined for your un-identified visitors.', 'Admin.Shopparameters.Help'),
            ])
            ->add('guests_group', ChoiceType::class, [
                'choices' => $choices,
                'label' => $this->trans('Guests group', 'Admin.Shopparameters.Feature'),
                'help' => $this->trans('The group defined for your identified guest customers (used in guest checkout).', 'Admin.Shopparameters.Help'),
            ])
            ->add('customers_group', ChoiceType::class, [
                'choices' => $choices,
                'label' => $this->trans('Customers group', 'Admin.Shopparameters.Feature'),
                'help' => $this->trans('The group defined for your identified registered customers.', 'Admin.Shopparameters.Help'),
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'translation_domain' => 'Admin.Shopparameters.Feature',
        ]);
    }
}
