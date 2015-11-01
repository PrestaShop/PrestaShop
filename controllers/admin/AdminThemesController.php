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

/**
 * @property Theme $object
 */
class AdminThemesControllerCore extends AdminController
{
    const MAX_NAME_LENGTH = 128;

    public function __construct()
    {
        $this->bootstrap = true;
        parent::__construct();
    }

    public function init()
    {
        parent::init();
        $this->can_display_themes = (!Shop::isFeatureActive() || Shop::getContext() == Shop::CONTEXT_SHOP);

        $themes = $this->themeManager->getThemes();
        if (count($themes) > 1) {
            $this->fields_options['theme'] = array(
                'title' => sprintf($this->l('Select a theme for the "%s" shop'), $this->context->shop->name),
                'description' => (!$this->can_display_themes) ? $this->l('You must select a shop from the above list if you wish to choose a theme.') : '',
                'fields' => array(
                    'theme_for_shop' => array(
                        'type' => 'theme',
                        'themes' => $themes,
                        'theme_current' => $this->context->shop->theme->directory,
                        'can_display_themes' => $this->can_display_themes,
                        'no_multishop_checkbox' => true
                    ),
                ),
            );
        }
    }

    /**
     * Function used to render the options for this controller
     */
    public function renderOptions()
    {
        if (isset($this->display) && method_exists($this, 'render'.$this->display)) {
            return $this->{'render'.$this->display}();
        }
        if ($this->fields_options && is_array($this->fields_options)) {
            $helper = new HelperOptions($this);
            $this->setHelperDisplay($helper);
            $helper->toolbar_scroll = true;
            $helper->title = $this->l('Theme appearance');
            $helper->toolbar_btn = array(
                'save' => array(
                    'href' => '#',
                    'desc' => $this->l('Save')
                )
            );
            $helper->id = $this->id;
            $helper->tpl_vars = $this->tpl_option_vars;
            $options = $helper->generateOptions($this->fields_options);

            return $options;
        }
    }

    public function setMedia()
    {
        parent::setMedia();
        $this->addJS(_PS_JS_DIR_.'admin/themes.js');

        if ($this->context->mode == Context::MODE_HOST && Tools::getValue('action') == 'importtheme') {
            $this->addJS(_PS_JS_DIR_.'admin/addons.js');
        }
    }
}
