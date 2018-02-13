<?php
/**
 * 2007-2018 PrestaShop
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

class AttributeLangCore extends DataLangCore
{
    // Don't replace domain in init() with $this->domain for translation parsing
    protected $domain = 'Shop.Demo.Catalog';

    protected $keys = array('id_attribute');

    protected $fieldsToUpdate = array('name');

    protected function init()
    {
        $this->fieldNames = array(
            'name' => array(
                md5('S') => $this->translator->trans('S', array(), 'Shop.Demo.Catalog', $this->locale),
                md5('M') => $this->translator->trans('M', array(), 'Shop.Demo.Catalog', $this->locale),
                md5('L') => $this->translator->trans('L', array(), 'Shop.Demo.Catalog', $this->locale),
                md5('One size') => $this->translator->trans('One size', array(), 'Shop.Demo.Catalog', $this->locale),
                md5('Grey') => $this->translator->trans('Grey', array(), 'Shop.Demo.Catalog', $this->locale),
                md5('Taupe') => $this->translator->trans('Taupe', array(), 'Shop.Demo.Catalog', $this->locale),
                md5('Beige') => $this->translator->trans('Beige', array(), 'Shop.Demo.Catalog', $this->locale),
                md5('White') => $this->translator->trans('White', array(), 'Shop.Demo.Catalog', $this->locale),
                md5('Off White') => $this->translator->trans('Off White', array(), 'Shop.Demo.Catalog', $this->locale),
                md5('Red') => $this->translator->trans('Red', array(), 'Shop.Demo.Catalog', $this->locale),
                md5('Black') => $this->translator->trans('Black', array(), 'Shop.Demo.Catalog', $this->locale),
                md5('Camel') => $this->translator->trans('Camel', array(), 'Shop.Demo.Catalog', $this->locale),
                md5('Orange') => $this->translator->trans('Orange', array(), 'Shop.Demo.Catalog', $this->locale),
                md5('Blue') => $this->translator->trans('Blue', array(), 'Shop.Demo.Catalog', $this->locale),
                md5('Green') => $this->translator->trans('Green', array(), 'Shop.Demo.Catalog', $this->locale),
                md5('Yellow') => $this->translator->trans('Yellow', array(), 'Shop.Demo.Catalog', $this->locale),
                md5('Brown') => $this->translator->trans('Brown', array(), 'Shop.Demo.Catalog', $this->locale),
                md5('35') => $this->translator->trans('35', array(), 'Shop.Demo.Catalog', $this->locale),
                md5('36') => $this->translator->trans('36', array(), 'Shop.Demo.Catalog', $this->locale),
                md5('37') => $this->translator->trans('37', array(), 'Shop.Demo.Catalog', $this->locale),
                md5('38') => $this->translator->trans('38', array(), 'Shop.Demo.Catalog', $this->locale),
                md5('39') => $this->translator->trans('39', array(), 'Shop.Demo.Catalog', $this->locale),
                md5('40') => $this->translator->trans('40', array(), 'Shop.Demo.Catalog', $this->locale),
                md5('Pink') => $this->translator->trans('Pink', array(), 'Shop.Demo.Catalog', $this->locale),
            )
        );
    }
}
