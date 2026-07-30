<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\Form\Admin\Configure\ShopParameters\CustomerGroup;

use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class DefaultGroupsType extends TranslatorAwareType
{
    public function __construct(
        TranslatorInterface $translator,
        array $locales,
        private readonly array $groupChoices,
    ) {
        parent::__construct($translator, $locales);
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('unidentified_group', ChoiceType::class, [
                'label' => $this->trans('Visitors group', 'Admin.Shopparameters.Feature'),
                'choices' => $this->groupChoices,
                'help' => $this->trans('The group to which unregistered visitors are assigned.', 'Admin.Shopparameters.Help'),
            ])
            ->add('guest_group', ChoiceType::class, [
                'label' => $this->trans('Guests group', 'Admin.Shopparameters.Feature'),
                'choices' => $this->groupChoices,
                'help' => $this->trans('The group to which guest checkout customers are assigned.', 'Admin.Shopparameters.Help'),
            ])
            ->add('customer_group', ChoiceType::class, [
                'label' => $this->trans('Registered customers group', 'Admin.Shopparameters.Feature'),
                'choices' => $this->groupChoices,
                'help' => $this->trans('The group to which newly registered customers are assigned.', 'Admin.Shopparameters.Help'),
            ])
        ;
    }
}
