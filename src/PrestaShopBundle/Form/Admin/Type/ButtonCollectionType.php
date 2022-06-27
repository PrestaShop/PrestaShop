<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
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
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                'label' => false,
                'buttons' => [],
                'justify_content' => 'space-between',
            ])
            ->setAllowedTypes('buttons', 'array')
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
