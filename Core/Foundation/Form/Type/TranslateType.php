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

namespace PrestaShop\PrestaShop\Core\Foundation\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;

/**
 * This form class is risponsible to create a translatable form
 */
class TranslateType extends AbstractType
{
    private $type;
    private $options;
    private $locales;

    /**
     * Constructor
     *
     * @param string $type The field type
     * @param array $options The field options as constraints, attributes...
     * @param array $locales The locales to render all fields
     */
    public function __construct($type, $options = array(), $locales = array())
    {
        $this->type = $type;
        $this->options = $options;
        $this->locales = empty($locales) ? \Language::getLanguages() : $locales;
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
            $this->options['label'] = $locale['iso_code'];
            if ($i>0) {
                $this->options['required'] = false;
            }
            $builder->add($locale['id_lang'], $this->type, $this->options);
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
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'translatefields';
    }
}
