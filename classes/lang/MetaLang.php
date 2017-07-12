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

class MetaLangCore extends DataLangCore
{
    // Don't replace domain in init() with $this->domain for translation parsing
    protected $domain = 'Shop.Navigation';

    protected $keys = array('id_meta', 'id_shop');

    protected $fieldsToUpdate = array('title', 'description', 'url_rewrite');

    protected function init()
    {
        $this->fieldNames = array(
            'title' => array(
                md5('404 error') => $this->translator->trans('404 error', array(), 'Shop.Navigation', $this->locale),
                md5('Best sales') => $this->translator->trans('Best sales', array(), 'Shop.Navigation', $this->locale),
                md5('Contact us') => $this->translator->trans('Contact us', array(), 'Shop.Navigation', $this->locale),
                md5('Manufacturers') => $this->translator->trans('Manufacturers', array(), 'Shop.Navigation', $this->locale),
                md5('New products') => $this->translator->trans('New products', array(), 'Shop.Navigation', $this->locale),
                md5('Forgot your password') => $this->translator->trans('Forgot your password', array(), 'Shop.Navigation', $this->locale),
                md5('Prices drop') => $this->translator->trans('Prices drop', array(), 'Shop.Navigation', $this->locale),
                md5('Sitemap') => $this->translator->trans('Sitemap', array(), 'Shop.Navigation', $this->locale),
                md5('Suppliers') => $this->translator->trans('Suppliers', array(), 'Shop.Navigation', $this->locale),
                md5('Address') => $this->translator->trans('Address', array(), 'Shop.Navigation', $this->locale),
                md5('Addresses') => $this->translator->trans('Addresses', array(), 'Shop.Navigation', $this->locale),
                md5('Login') => $this->translator->trans('Login', array(), 'Shop.Navigation', $this->locale),
                md5('Cart') => $this->translator->trans('Cart', array(), 'Shop.Navigation', $this->locale),
                md5('Discount') => $this->translator->trans('Discount', array(), 'Shop.Navigation', $this->locale),
                md5('Order history') => $this->translator->trans('Order history', array(), 'Shop.Navigation', $this->locale),
                md5('Identity') => $this->translator->trans('Identity', array(), 'Shop.Navigation', $this->locale),
                md5('My account') => $this->translator->trans('My account', array(), 'Shop.Navigation', $this->locale),
                md5('Order follow') => $this->translator->trans('Order follow', array(), 'Shop.Navigation', $this->locale),
                md5('Credit slip') => $this->translator->trans('Credit slip', array(), 'Shop.Navigation', $this->locale),
                md5('Order') => $this->translator->trans('Order', array(), 'Shop.Navigation', $this->locale),
                md5('Search') => $this->translator->trans('Search', array(), 'Shop.Navigation', $this->locale),
                md5('Stores') => $this->translator->trans('Stores', array(), 'Shop.Navigation', $this->locale),
                md5('Guest tracking') => $this->translator->trans('Guest tracking', array(), 'Shop.Navigation', $this->locale),
                md5('Order confirmation') => $this->translator->trans('Order confirmation', array(), 'Shop.Navigation', $this->locale),
            ),
            'description' => array(
                md5('This page cannot be found')
                    => $this->translator->trans('This page cannot be found', array(), 'Shop.Navigation', $this->locale),

                md5('Our best sales')
                    => $this->translator->trans('Our best sales', array(), 'Shop.Navigation', $this->locale),

                md5('Use our form to contact us')
                    => $this->translator->trans('Use our form to contact us', array(), 'Shop.Navigation', $this->locale),

                md5('Shop powered by PrestaShop')
                    => $this->translator->trans('Shop powered by PrestaShop', array(), 'Shop.Navigation', $this->locale),

                md5('Manufacturers list')
                    => $this->translator->trans('Brand list', array(), 'Shop.Navigation', $this->locale),

                md5('Our new products')
                    => $this->translator->trans('Our new products', array(), 'Shop.Navigation', $this->locale),

                md5('Enter the e-mail address you use to sign in to receive an e-mail with a new password')
                    => $this->translator->trans('Enter the e-mail address you use to sign in to receive an e-mail with a new password', array(), 'Shop.Navigation', $this->locale),

                md5('Our special products')
                    => $this->translator->trans('On-sale products', array(), 'Shop.Navigation', $this->locale),

                md5('Lost ? Find what your are looking for')
                    => $this->translator->trans('Lost ? Find what your are looking for', array(), 'Shop.Navigation', $this->locale),

                md5('Suppliers list')
                    => $this->translator->trans('Suppliers list', array(), 'Shop.Navigation', $this->locale),
            ),
            'url_rewrite' => array(
                md5('page-not-found') => $this->translator->trans('page-not-found', array(), 'Shop.Navigation', $this->locale),
                md5('best-sales') => $this->translator->trans('best-sales', array(), 'Shop.Navigation', $this->locale),
                md5('contact-us') => $this->translator->trans('contact-us', array(), 'Shop.Navigation', $this->locale),
                md5('manufacturers') => $this->translator->trans('manufacturers', array(), 'Shop.Navigation', $this->locale),
                md5('new-products') => $this->translator->trans('new-products', array(), 'Shop.Navigation', $this->locale),
                md5('password-recovery') => $this->translator->trans('password-recovery', array(), 'Shop.Navigation', $this->locale),
                md5('prices-drop') => $this->translator->trans('prices-drop', array(), 'Shop.Navigation', $this->locale),
                md5('sitemap') => $this->translator->trans('sitemap', array(), 'Shop.Navigation', $this->locale),
                md5('supplier') => $this->translator->trans('supplier', array(), 'Shop.Navigation', $this->locale),
                md5('address') => $this->translator->trans('address', array(), 'Shop.Navigation', $this->locale),
                md5('addresses') => $this->translator->trans('addresses', array(), 'Shop.Navigation', $this->locale),
                md5('login') => $this->translator->trans('login', array(), 'Shop.Navigation', $this->locale),
                md5('cart') => $this->translator->trans('cart', array(), 'Shop.Navigation', $this->locale),
                md5('discount') => $this->translator->trans('discount', array(), 'Shop.Navigation', $this->locale),
                md5('order-history') => $this->translator->trans('order-history', array(), 'Shop.Navigation', $this->locale),
                md5('identity') => $this->translator->trans('identity', array(), 'Shop.Navigation', $this->locale),
                md5('my-account') => $this->translator->trans('my-account', array(), 'Shop.Navigation', $this->locale),
                md5('order-follow') => $this->translator->trans('order-follow', array(), 'Shop.Navigation', $this->locale),
                md5('credit-slip') => $this->translator->trans('credit-slip', array(), 'Shop.Navigation', $this->locale),
                md5('order') => $this->translator->trans('order', array(), 'Shop.Navigation', $this->locale),
                md5('search') => $this->translator->trans('search', array(), 'Shop.Navigation', $this->locale),
                md5('stores') => $this->translator->trans('stores', array(), 'Shop.Navigation', $this->locale),
                md5('guest-tracking') => $this->translator->trans('guest-tracking', array(), 'Shop.Navigation', $this->locale),
                md5('order-confirmation') => $this->translator->trans('order-confirmation', array(), 'Shop.Navigation', $this->locale),
            ),
        );
    }
}
