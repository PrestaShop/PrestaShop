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

class MetaLang
{
    /** @var Translator  */
    protected $translator;

    /** @var string */
    protected $locale;

    protected function init()
    {
         $this->translator->trans('404 error', array(), 'Shop.Navigation', $this->locale);
         $this->translator->trans('Best sales', array(), 'Shop.Navigation', $this->locale);
         $this->translator->trans('Contact us', array(), 'Shop.Navigation', $this->locale);
         $this->translator->trans('Manufacturers', array(), 'Shop.Navigation', $this->locale);
         $this->translator->trans('New products', array(), 'Shop.Navigation', $this->locale);
         $this->translator->trans('Forgot your password', array(), 'Shop.Navigation', $this->locale);
         $this->translator->trans('Prices drop', array(), 'Shop.Navigation', $this->locale);
         $this->translator->trans('Sitemap', array(), 'Shop.Navigation', $this->locale);
         $this->translator->trans('Suppliers', array(), 'Shop.Navigation', $this->locale);
         $this->translator->trans('Address', array(), 'Shop.Navigation', $this->locale);
         $this->translator->trans('Addresses', array(), 'Shop.Navigation', $this->locale);
         $this->translator->trans('Login', array(), 'Shop.Navigation', $this->locale);
         $this->translator->trans('Cart', array(), 'Shop.Navigation', $this->locale);
         $this->translator->trans('Discount', array(), 'Shop.Navigation', $this->locale);
         $this->translator->trans('Order history', array(), 'Shop.Navigation', $this->locale);
         $this->translator->trans('Identity', array(), 'Shop.Navigation', $this->locale);
         $this->translator->trans('My account', array(), 'Shop.Navigation', $this->locale);
         $this->translator->trans('Order follow', array(), 'Shop.Navigation', $this->locale);
         $this->translator->trans('Credit slip', array(), 'Shop.Navigation', $this->locale);
         $this->translator->trans('Order', array(), 'Shop.Navigation', $this->locale);
         $this->translator->trans('Search', array(), 'Shop.Navigation', $this->locale);
         $this->translator->trans('Stores', array(), 'Shop.Navigation', $this->locale);
         $this->translator->trans('Guest tracking', array(), 'Shop.Navigation', $this->locale);
         $this->translator->trans('Order confirmation', array(), 'Shop.Navigation', $this->locale);
         $this->translator->trans('This page cannot be found', array(), 'Shop.Navigation', $this->locale);

         $this->translator->trans('Our best sales', array(), 'Shop.Navigation', $this->locale);

         $this->translator->trans('Use our form to contact us', array(), 'Shop.Navigation', $this->locale);

         $this->translator->trans('Shop powered by PrestaShop', array(), 'Shop.Navigation', $this->locale);

         $this->translator->trans('Brand list', array(), 'Shop.Navigation', $this->locale);

         $this->translator->trans('Our new products', array(), 'Shop.Navigation', $this->locale);

         $this->translator->trans('Enter the e-mail address you use to sign in to receive an e-mail with a new password', array(), 'Shop.Navigation', $this->locale);

         $this->translator->trans('On-sale products', array(), 'Shop.Navigation', $this->locale);

         $this->translator->trans('Lost ? Find what your are looking for', array(), 'Shop.Navigation', $this->locale);

         $this->translator->trans('Suppliers list', array(), 'Shop.Navigation', $this->locale);
         $this->translator->trans('page-not-found', array(), 'Shop.Navigation', $this->locale);
         $this->translator->trans('best-sales', array(), 'Shop.Navigation', $this->locale);
         $this->translator->trans('contact-us', array(), 'Shop.Navigation', $this->locale);
         $this->translator->trans('manufacturers', array(), 'Shop.Navigation', $this->locale);
         $this->translator->trans('new-products', array(), 'Shop.Navigation', $this->locale);
         $this->translator->trans('password-recovery', array(), 'Shop.Navigation', $this->locale);
         $this->translator->trans('prices-drop', array(), 'Shop.Navigation', $this->locale);
         $this->translator->trans('sitemap', array(), 'Shop.Navigation', $this->locale);
         $this->translator->trans('supplier', array(), 'Shop.Navigation', $this->locale);
         $this->translator->trans('address', array(), 'Shop.Navigation', $this->locale);
         $this->translator->trans('addresses', array(), 'Shop.Navigation', $this->locale);
         $this->translator->trans('login', array(), 'Shop.Navigation', $this->locale);
         $this->translator->trans('cart', array(), 'Shop.Navigation', $this->locale);
         $this->translator->trans('discount', array(), 'Shop.Navigation', $this->locale);
         $this->translator->trans('order-history', array(), 'Shop.Navigation', $this->locale);
         $this->translator->trans('identity', array(), 'Shop.Navigation', $this->locale);
         $this->translator->trans('my-account', array(), 'Shop.Navigation', $this->locale);
         $this->translator->trans('order-follow', array(), 'Shop.Navigation', $this->locale);
         $this->translator->trans('credit-slip', array(), 'Shop.Navigation', $this->locale);
         $this->translator->trans('order', array(), 'Shop.Navigation', $this->locale);
         $this->translator->trans('search', array(), 'Shop.Navigation', $this->locale);
         $this->translator->trans('stores', array(), 'Shop.Navigation', $this->locale);
         $this->translator->trans('guest-tracking', array(), 'Shop.Navigation', $this->locale);
         $this->translator->trans('order-confirmation', array(), 'Shop.Navigation', $this->locale);
    }
}
