<?php
/**
 * 2007-2018 PrestaShop.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
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
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShopBundle\Form\Admin\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class TranslatableType adds translatable inputs with custom inner type to forms.
 */
class TranslatableType extends AbstractType
{
    /**
     * @var array
     */
    private $locales;

    /**
     * @param array $locales
     */
    public function __construct(array $locales)
    {
        $this->locales = $locales;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        foreach ($options['locales'] as $locale) {
            $typeOptions = $options['options'];
            $typeOptions['label'] = $locale['iso_code'];

            if (!isset($typeOptions['required'])) {
                $typeOptions['required'] = false;
            }

            $builder->add($locale['id_lang'], $options['type'], $typeOptions);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['locales'] = $options['locales'];
        $view->vars['default_locale'] = reset($options['locales']);
        $view->vars['hide_locales'] = 1 >= count($options['locales']);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'type' => TextType::class,
            'options' => [],
            'locales' => $this->locales,
        ]);

        $resolver->setAllowedTypes('locales', 'array');
        $resolver->setAllowedTypes('options', 'array');
        $resolver->setAllowedTypes('type', 'string');
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'translatable';
    }
}
