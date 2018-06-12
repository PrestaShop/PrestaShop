<?php
/**
 * 2007-2017 PrestaShop
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
 * @copyright 2007-2017 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShopBundle\Translation\KeysReference\Xlang;

use PrestaShopBundle\Translation\TranslatorComponent as Translator;

class AttributeLang
{
    /** @var Translator  */
    protected $translator;

    /** @var string */
    protected $locale;

    protected function init()
    {
         $this->translator->trans('S', array(), 'Shop.Demo.Catalog', $this->locale);
         $this->translator->trans('M', array(), 'Shop.Demo.Catalog', $this->locale);
         $this->translator->trans('L', array(), 'Shop.Demo.Catalog', $this->locale);
         $this->translator->trans('One size', array(), 'Shop.Demo.Catalog', $this->locale);
         $this->translator->trans('Grey', array(), 'Shop.Demo.Catalog', $this->locale);
         $this->translator->trans('Taupe', array(), 'Shop.Demo.Catalog', $this->locale);
         $this->translator->trans('Beige', array(), 'Shop.Demo.Catalog', $this->locale);
         $this->translator->trans('White', array(), 'Shop.Demo.Catalog', $this->locale);
         $this->translator->trans('Off White', array(), 'Shop.Demo.Catalog', $this->locale);
         $this->translator->trans('Red', array(), 'Shop.Demo.Catalog', $this->locale);
         $this->translator->trans('Black', array(), 'Shop.Demo.Catalog', $this->locale);
         $this->translator->trans('Camel', array(), 'Shop.Demo.Catalog', $this->locale);
         $this->translator->trans('Orange', array(), 'Shop.Demo.Catalog', $this->locale);
         $this->translator->trans('Blue', array(), 'Shop.Demo.Catalog', $this->locale);
         $this->translator->trans('Green', array(), 'Shop.Demo.Catalog', $this->locale);
         $this->translator->trans('Yellow', array(), 'Shop.Demo.Catalog', $this->locale);
         $this->translator->trans('Brown', array(), 'Shop.Demo.Catalog', $this->locale);
         $this->translator->trans('35', array(), 'Shop.Demo.Catalog', $this->locale);
         $this->translator->trans('36', array(), 'Shop.Demo.Catalog', $this->locale);
         $this->translator->trans('37', array(), 'Shop.Demo.Catalog', $this->locale);
         $this->translator->trans('38', array(), 'Shop.Demo.Catalog', $this->locale);
         $this->translator->trans('39', array(), 'Shop.Demo.Catalog', $this->locale);
         $this->translator->trans('40', array(), 'Shop.Demo.Catalog', $this->locale);
         $this->translator->trans('Pink', array(), 'Shop.Demo.Catalog', $this->locale);
    }
}
