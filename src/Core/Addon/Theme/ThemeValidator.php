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

namespace PrestaShop\PrestaShop\Core\Addon\Theme;

use PrestaShop\PrestaShop\Core\ConfigurationInterface;
use Symfony\Component\Translation\TranslatorInterface;

class ThemeValidator
{
    /**
     * Translator.
     *
     * @var \Symfony\Component\Translation\TranslatorInterface
     */
    private $translator;
    private $appConfiguration;

    private $errors = array();

    public function __construct(TranslatorInterface $translator, ConfigurationInterface $configuration)
    {
        $this->translator = $translator;
        $this->appConfiguration = $configuration;
    }

    public function getErrors($themeName)
    {
        return array_key_exists($themeName, $this->errors) ? $this->errors[$themeName] : false;
    }

    public function isValid(Theme $theme)
    {
        return $this->hasRequiredFiles($theme)
            && $this->hasRequiredProperties($theme);
    }

    private function hasRequiredProperties(Theme $theme)
    {
        $themeName = $theme->getName();

        foreach ($this->getRequiredProperties() as $prop) {
            if (!$theme->has($prop)) {
                if (!array_key_exists($themeName, $this->errors)) {
                    $this->errors[$themeName] = array();
                }

                $this->errors[$themeName] = $this->translator->trans(
                    'An error occurred. The information "%s" is missing.', array($prop), 'Admin.Design.Notification'
                );
            }
        }

        return !array_key_exists($themeName, $this->errors);
    }

    public function getRequiredProperties()
    {
        return array(
            'name',
            'display_name',
            'version',
            'author.name',
            'meta.compatibility.from',
            'meta.available_layouts',
            'global_settings.image_types.cart_default',
            'global_settings.image_types.small_default',
            'global_settings.image_types.medium_default',
            'global_settings.image_types.large_default',
            'global_settings.image_types.home_default',
            'global_settings.image_types.category_default',
            'theme_settings.default_layout',
        );
    }

    private function hasRequiredFiles(Theme $theme)
    {
        $themeName = $theme->getName();
        $parentDir = realpath($this->appConfiguration->get('_PS_ALL_THEMES_DIR_').$theme->get('parent')).'/';
        $parentFile = false;

        foreach ($this->getRequiredFiles() as $file) {
            $childFile = $theme->getDirectory().$file;
            if ($theme->get('parent')) {
                $parentFile = $parentDir.$file;
            }

            if (!file_exists($childFile) && !file_exists($parentFile)) {
                if (!array_key_exists($themeName, $this->errors)) {
                    $this->errors[$themeName] = array();
                }

                $this->errors[$themeName] = $this->translator->trans('An error occurred. The template "%s" is missing.', array($file), 'Admin.Design.Notification');
            }
        }

        return !array_key_exists($themeName, $this->errors);
    }

    public function getRequiredFiles()
    {
        return array(
            'preview.png',
            'config/theme.yml',
            'assets/js/theme.js',
            'assets/css/theme.css',
            // Templates
            'templates/_partials/form-fields.tpl',
            'templates/catalog/product.tpl',
            'templates/catalog/listing/product-list.tpl',
            'templates/checkout/cart.tpl',
            'templates/checkout/checkout.tpl',
            'templates/cms/category.tpl',
            'templates/cms/page.tpl',
            'templates/customer/address.tpl',
            'templates/customer/addresses.tpl',
            'templates/customer/guest-tracking.tpl',
            'templates/customer/guest-login.tpl',
            'templates/customer/history.tpl',
            'templates/customer/identity.tpl',
            'templates/index.tpl',
            'templates/customer/my-account.tpl',
            'templates/checkout/order-confirmation.tpl',
            'templates/customer/order-detail.tpl',
            'templates/customer/order-follow.tpl',
            'templates/customer/order-return.tpl',
            'templates/customer/order-slip.tpl',
            'templates/errors/404.tpl',
            'templates/errors/forbidden.tpl',
            'templates/checkout/cart-empty.tpl',
            'templates/cms/sitemap.tpl',
            'templates/cms/stores.tpl',
            'templates/customer/authentication.tpl',
            'templates/customer/registration.tpl',
            'templates/contact.tpl',
        );
    }
}
