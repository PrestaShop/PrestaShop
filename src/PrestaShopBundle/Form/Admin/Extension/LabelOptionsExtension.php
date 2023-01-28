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

namespace PrestaShopBundle\Form\Admin\Extension;

use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * For some forms we need extra options to configure the label rendering, we cannot use the existing
 * label_attr option because it adds attributes directly on the label. These extra options are used
 * inside our PrestaShop UI kit form theme.
 *
 * Form theme path: src/PrestaShopBundle/Resources/views/Admin/TwigTemplateForm/prestashop_ui_kit_base.html.twig
 */
class LabelOptionsExtension extends AbstractTypeExtension
{
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                // Allows to use a different kind of HTML tag in place of the label, e.g: label_tag_name => h2
                'label_tag_name' => null,
                // Allows to add a subtitle after a label (mostly useful when using label_tag_name with header tags)
                'label_subtitle' => null,
                // Allows to add a help box after a label
                'label_help_box' => null,
                // Allows to force a label in a tab content when using the NavigationTabType (by default the label value is only used for tab name)
                'label_tab' => null,
            ])
            ->setAllowedTypes('label_tag_name', ['null', 'string'])
            ->setAllowedTypes('label_subtitle', ['null', 'string'])
            ->setAllowedTypes('label_help_box', ['null', 'string'])
            ->setAllowedTypes('label_tab', ['null', 'string'])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        if (!empty($options['label_tag_name'])) {
            $view->vars['label_tag_name'] = $options['label_tag_name'];
        }
        if (!empty($options['label_subtitle'])) {
            $view->vars['label_subtitle'] = $options['label_subtitle'];
        }
        if (!empty($options['label_help_box'])) {
            $view->vars['label_help_box'] = $options['label_help_box'];
        }
        if (!empty($options['label_tab'])) {
            $view->vars['label_tab'] = $options['label_tab'];
        }
    }

    /**
     * {@inheritdoc}
     */
    public static function getExtendedTypes(): iterable
    {
        return [FormType::class];
    }
}
