<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\Form\Extension;

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
