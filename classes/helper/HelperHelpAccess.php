<?php
/*
* 2007-2013 PrestaShop
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
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2013 PrestaShop SA
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

/**
 * @since 1.5
 */
class HelperHelpAccessCore extends Helper
{
    public $label;
    public $iso_lang;
    public $country;
    public $ps_version;

    public function __construct($label, $iso_lang, $country, $ps_version)
    {
        parent::__construct();
        $this->base_folder = 'helpers/help_access/';

        $this->tpl = $this->createTemplate('button.tpl');
        $this->label = $label;
        $this->iso_lang = $iso_lang;
        $this->country = $country;
        $this->ps_version = $ps_version;
    }

    /**
     * @return string|void
     */
    public function generate()
    {
        $info = HelpAccess::retrieveInfos($this->label, $this->iso_lang, $this->country, $this->ps_version);
        $content = '';

        if (array_key_exists('version', $info) && $info['version'] != '')
        {
            $last_version = HelpAccess::getVersion($this->label);

            $tpl_vars['button_class'] = 'process-icon-help';
            if ($last_version < $info['version'])
                $tpl_vars['button_class'] = 'process-icon-help-new';

            $tpl_vars['label'] = $this->label;
            $tpl_vars['iso_lang'] = $this->iso_lang;
            $tpl_vars['country'] = $this->country;
            $tpl_vars['version'] = $this->ps_version;
            $tpl_vars['doc_version'] = $info['version'];
            $tpl_vars['help_base_url'] = HelpAccess::URL;
            $tpl_vars['tooltip'] = $info['tooltip'];

            $this->tpl->assign($tpl_vars);

            $content = parent::generate();
        }

        return $content;
    }
}