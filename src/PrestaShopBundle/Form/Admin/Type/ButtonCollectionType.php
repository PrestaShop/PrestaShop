<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\Form\Admin\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Exception\InvalidArgumentException;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * ButtonCollectionType is a form type used to group buttons in a common form group which
 * is useful for forms which have multiple submit buttons.
 *
 * $builder
 *     ->add('buttons', ButtonCollectionType::class, [
 *         'buttons' => [
 *             'save' => SubmitType::class,
 *             'cancel' => [
 *                 'type' => SubmitType::class,
 *                 'options' => [
 *                     'label' => 'Cancel',
 *                 ],
 *                 'group' => 'left',
 *             ],
 *         ],
 *     ])
 * ;
 */
class ButtonCollectionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        foreach ($options['buttons'] as $buttonOptions) {
            $builder->add($buttonOptions['name'], $buttonOptions['type'], $buttonOptions['options']);
        }
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        parent::buildView($view, $form, $options);

        $buttonsOptions = $options['buttons'];
        $buttonGroups = [];
        foreach ($buttonsOptions as $buttonOptions) {
            $buttonGroups[$buttonOptions['group']][] = $buttonOptions['name'];
        }
        $view->vars['button_groups'] = $buttonGroups;
        $view->vars['justify_content'] = $options['justify_content'];
        $view->vars['inline_buttons_limit'] = $options['inline_buttons_limit'];
        $view->vars['use_inline_labels'] = $options['use_inline_labels'];
        $view->vars['use_button_groups'] = $options['use_button_groups'];
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                'label' => false,
                'buttons' => [],
                'justify_content' => 'space-between',
                'inline_buttons_limit' => null,
                'use_inline_labels' => true,
                // Use bootstrap buttons groups (buttons stick to each other as a group)
                'use_button_groups' => false,
            ])
            ->setAllowedTypes('buttons', 'array')
            ->setAllowedTypes('inline_buttons_limit', ['int', 'null'])
            ->setNormalizer('buttons', function (Options $options, $buttons) {
                $resolver = $this->getButtonOptionsResolver();
                $normalizedOptions = [];

                foreach ($buttons as $buttonName => $options) {
                    if (is_string($options)) {
                        $options = [
                            'type' => $options,
                        ];
                    }

                    $options['name'] = $options['name'] ?? $buttonName;
                    $normalizedOptions[$options['name']] = $resolver->resolve($options);
                }

                return $normalizedOptions;
            })
        ;
    }

    private function getButtonOptionsResolver(): OptionsResolver
    {
        $resolver = new OptionsResolver();
        $resolver
            ->setDefault('name', '')
            ->setDefault('options', [])
            ->setDefault('group', 'default')
            ->setRequired('type')
            ->setAllowedTypes('options', 'array')
            ->setAllowedTypes('name', 'string')
            ->setAllowedTypes('type', 'string')
            ->setAllowedTypes('group', 'string')
            ->setNormalizer('type', function (Options $options, $value) {
                if (!class_exists($value)) {
                    throw new InvalidArgumentException('Invalid button type provided, expected a FQCN string.');
                }

                return $value;
            })
        ;

        return $resolver;
    }
}
