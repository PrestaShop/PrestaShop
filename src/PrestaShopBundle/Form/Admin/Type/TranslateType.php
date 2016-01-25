<?php
/**
 * 2007-2015 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
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
 * @copyright 2007-2015 PrestaShop SA
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */
namespace PrestaShopBundle\Form\Admin\Type;

use PrestaShopBundle\Form\Admin\Type\CommonAbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;

/**
 * This form class is responsible to create a translatable form
 */
class TranslateType extends CommonAbstractType
{
    private $type;
    private $options;
    private $locales;
    private $hideTabs;

    /**
     * Constructor
     *
     * @param string $type The field type
     * @param array $options The field options as constraints, attributes...
     * @param array $locales The locales to render all fields
     * @param boolean $hideTabs The languages tabs will be removed but the tab panels are still here (to control them by JS)
     */
    public function __construct($type, $options = array(), $locales = array(), $hideTabs = true)
    {
        $this->type = $type;
        $this->options = $options;
        $this->locales = $locales;
        $this->hideTabs = $hideTabs;
    }

    /**
     * {@inheritdoc}
     *
     * Builds form fields for each locales
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $i=0;
        foreach ($this->locales as $locale) {
            $locale_options = $this->options;
            $locale_options['label'] = $locale['iso_code'];
            if ($i>0) {
                $locale_options['required'] = false;
                unset($locale_options['constraints']);
            }
            $builder->add($locale['id_lang'], $this->type, $locale_options);
            $i++;
        }
    }

    /**
     * {@inheritdoc}
     *
     * Add the var locales and defaultLocale to the view
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['locales'] = $this->locales;
        $view->vars['defaultLocale'] = $this->locales[0];
        $view->vars['hideTabs'] = $this->hideTabs;
    }

    /**
     * Returns the block prefix of this type.
     *
     * @return string The prefix name
     */
    public function getBlockPrefix()
    {
        return 'translatefields';
    }
}
