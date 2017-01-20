<?php
/**
 * 2007-2017 PrestaShop
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
 * @copyright 2007-2017 PrestaShop SA
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

class FeatureLangCore extends DataLangCore
{
    // Don't replace domain in init() with $this->domain for translation parsing
    protected $domain = 'Shop.Demo.Catalog';

    protected $keys = array('id_feature');

    protected $fieldsToUpdate = array('name');

    protected function init()
    {
        $this->fieldNames = array(
            'name' => array(
                md5('Height') => $this->translator->trans('Height', array(), 'Shop.Demo.Catalog', $this->locale),
                md5('Width') => $this->translator->trans('Width', array(), 'Shop.Demo.Catalog', $this->locale),
                md5('Depth') => $this->translator->trans('Depth', array(), 'Shop.Demo.Catalog', $this->locale),
                md5('Weight') => $this->translator->trans('Weight', array(), 'Shop.Demo.Catalog', $this->locale),
                md5('Compositions') => $this->translator->trans('Compositions', array(), 'Shop.Demo.Catalog', $this->locale),
                md5('Styles') => $this->translator->trans('Styles', array(), 'Shop.Demo.Catalog', $this->locale),
                md5('Properties') => $this->translator->trans('Properties', array(), 'Shop.Demo.Catalog', $this->locale),
            ),
        );
    }
}
