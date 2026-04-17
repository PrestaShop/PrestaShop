<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Form\Admin\Type;

use PrestaShopBundle\Form\FormBuilderModifier;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Exception\InvalidArgumentException;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * This form type includes automatically a choice type (by default radio buttons) that
 * allows toggling between its children. The choices of the radios are automatically built
 * based on the children this form type contains.
 *
 * Usage:
 *   - create a compound type (that extends AbstractType for example) and override the getParent method
 *     so it returns this form type as parent (ToggleChildrenChoiceType::class)
 *   - your form must use the prestashop UI kit form theme (via its parent, via twig form_theme, or via the form_theme option)
 *   - you will need some JS code to activate the toggle behaviour:
 *       window.prestashop.component.initComponents(['ToggleChildrenChoice']);
 *   - if you need a state where no child is selected you can define a placeholder option
 *
 *  Custom choice:
 *  You can override the type used for the choice element thanks to the choice_type option, and you can override or
 *  complement its options thanks to the choice_options option.
 */
class ToggleChildrenChoiceType extends AbstractType
{
    public function __construct(
        protected readonly FormBuilderModifier $formBuilderModifier,
    ) {
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);
        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) use ($options) {
            $this->addChildrenSelector($event->getForm(), $options);
        });
    }

    private function addChildrenSelector(FormInterface $form, array $options): void
    {
        $childrenChoices = [];
        foreach ($form->all() as $childName => $child) {
            if ($childName === 'children_selector') {
                throw new InvalidArgumentException('You can not use children_selector as a child name as this name is reserved for internal purposes');
            }
            $childrenChoices[$child->getConfig()->getOptions()['label'] ?? $childName] = $child->getName();
        }

        $defaultChoiceOptions = [
            'label' => $options['choice_options']['label'] ?? $options['label'] ?? false,
            'choices' => $childrenChoices,
            'placeholder' => $options['choice_options']['placeholder'] ?? $options['placeholder'] ?? null,
            'expanded' => true,
            'multiple' => false,
            'required' => false,
            'compound' => true,
        ];
        $form->add('children_selector', $options['choice_type'], $options['choice_options'] + $defaultChoiceOptions);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);
        $resolver->setDefaults([
            'form_theme' => '@PrestaShop/Admin/TwigTemplateForm/prestashop_ui_kit_base.html.twig',
            // You can override the choice element type
            'choice_type' => ChoiceType::class,
            // You can override the choice element options
            'choice_options' => [],
            'placeholder' => null,
        ]);
    }
}
