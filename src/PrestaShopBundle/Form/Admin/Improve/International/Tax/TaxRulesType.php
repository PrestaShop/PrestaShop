<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\Form\Admin\Improve\International\Tax;

use PrestaShopBundle\Form\Admin\Type\IconButtonType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Form type that renders the "Add a tax rule" button and the inline tax rules list.
 * Intended to be embedded in TaxRulesGroupType when editing an existing group.
 */
class TaxRulesType extends TranslatorAwareType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('add_tax_rule_btn', IconButtonType::class, [
            'label' => $this->trans('Add a tax rule', 'Admin.International.Feature'),
            'attr' => [
                'class' => 'js-add-tax-rule-btn btn btn-primary',
                'data-modal-title' => $this->trans('Add new tax rule', 'Admin.International.Feature'),
                'data-confirm-button-label' => $this->trans('Save', 'Admin.Actions'),
                'data-cancel-button-label' => $this->trans('Cancel', 'Admin.Actions'),
            ],
            'icon' => 'add_circle',
        ]);
    }

    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        parent::buildView($view, $form, $options);
        $view->vars['tax_rules_group_id'] = $options['tax_rules_group_id'];
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);
        $resolver->setDefaults([
            'form_theme' => '@PrestaShop/Admin/Improve/International/TaxRulesGroup/FormTheme/tax_rules.html.twig',
            'tax_rules_group_id' => null,
        ]);
        $resolver->setAllowedTypes('tax_rules_group_id', ['null', 'int']);
    }
}
