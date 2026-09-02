<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Form\Admin\Type\Material;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MaterialChoiceTreeType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $selectedData = [];
        if (null !== $form->getData()) {
            $selectedData = is_array($form->getData()) ? $form->getData() : [$form->getData()];
        }

        $view->vars['multiple'] = $options['multiple'];
        $view->vars['choices_tree'] = $this->getFormattedChoicesTree($options, $selectedData);
        $view->vars['choice_label'] = $options['choice_label'];
        $view->vars['choice_value'] = $options['choice_value'];
        $view->vars['choice_children'] = $options['choice_children'];
        $view->vars['disabled_values'] = $options['disabled_values'];
        $view->vars['selected_values'] = $selectedData;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                'choices_tree' => [],
                'choice_label' => 'name',
                'choice_value' => 'id',
                'choice_children' => 'children',
                'disabled_values' => [],
                'disabled' => false,
                'multiple' => false,
                'compound' => false,
            ])
            ->setAllowedTypes('choices_tree', 'array')
            ->setAllowedTypes('multiple', 'bool')
            ->setAllowedTypes('choice_value', 'string')
            ->setAllowedTypes('choice_label', 'string')
            ->setAllowedTypes('choice_children', 'string')
            ->setAllowedTypes('disabled_values', 'array')
            ->setAllowedTypes('disabled', 'bool')
            ->addAllowedValues('compound', false);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'material_choice_tree';
    }

    /**
     * @param array $options
     * @param array $selectedData
     *
     * @return array
     */
    private function getFormattedChoicesTree(array $options, array $selectedData)
    {
        $tree = $options['choices_tree'];

        foreach ($tree as &$choice) {
            $this->fillChoiceWithChildrenSelection(
                $choice,
                $options['choice_value'],
                $options['choice_children'],
                $selectedData
            );
        }

        return $tree;
    }

    /**
     * @param array $choice
     * @param string $choiceValueName
     * @param string $choiceChildrenName
     * @param array $selectedValues
     *
     * @return bool
     */
    private function fillChoiceWithChildrenSelection(
        array &$choice,
        $choiceValueName,
        $choiceChildrenName,
        array $selectedValues
    ) {
        $isSelected = false;
        $isChildrenSelected = false;

        if (in_array($choice[$choiceValueName], $selectedValues)) {
            $isSelected = true;
        }

        if (isset($choice[$choiceChildrenName])) {
            foreach ($choice[$choiceChildrenName] as &$child) {
                $selected = $this->fillChoiceWithChildrenSelection(
                    $child,
                    $choiceValueName,
                    $choiceChildrenName,
                    $selectedValues
                );

                if ($selected) {
                    $isChildrenSelected = true;
                }
            }
            unset($child);
        }

        $choice['has_selected_children'] = $isChildrenSelected;

        return $isSelected || $isChildrenSelected;
    }
}
