<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\Form\Admin\Grid;

use PrestaShop\PrestaShop\Core\Grid\View\DynamicDateRule;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Range;

class DynamicDateRuleType extends TranslatorAwareType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     *
     * @return void
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('date_rule', ChoiceType::class, [
                'choices' => $this->getRuleChoices(),
                'required' => true,
                'empty_data' => DynamicDateRule::KEEP_AS_IS->value,
            ])
            ->add('custom_days', IntegerType::class, [
                'required' => false,
                'constraints' => [
                    new Range(['min' => 1, 'max' => 3650]),
                ],
                'attr' => [
                    'placeholder' => $this->trans('Number of past days', 'Admin.Global'),
                    'min' => 1,
                ],
            ])
        ;
    }

    /**
     * @return array<string, string>
     */
    private function getRuleChoices(): array
    {
        return [
            $this->trans('Keep saved dates', 'Admin.Global') => DynamicDateRule::KEEP_AS_IS->value,
            $this->trans('Today', 'Admin.Global') => DynamicDateRule::TODAY->value,
            $this->trans('Yesterday', 'Admin.Global') => DynamicDateRule::YESTERDAY->value,
            $this->trans('Current week', 'Admin.Global') => DynamicDateRule::CURRENT_WEEK->value,
            $this->trans('Current month', 'Admin.Global') => DynamicDateRule::CURRENT_MONTH->value,
            $this->trans('Current quarter', 'Admin.Global') => DynamicDateRule::CURRENT_QUARTER->value,
            $this->trans('Current semester', 'Admin.Global') => DynamicDateRule::CURRENT_SEMESTER->value,
            $this->trans('Current year', 'Admin.Global') => DynamicDateRule::CURRENT_YEAR->value,
            $this->trans('Last X days', 'Admin.Global') => DynamicDateRule::LAST_DAYS->value,
        ];
    }
}
