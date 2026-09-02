<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Addon\Theme;

use PrestaShop\PrestaShop\Core\ConfigurationInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class ThemeValidator
{
    /**
     * Translator.
     *
     * @var TranslatorInterface
     */
    private $translator;
    private $appConfiguration;

    private $errors = [];

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
                    $this->errors[$themeName] = [];
                }

                $this->errors[$themeName][] = $this->translator->trans(
                    'An error occurred. The information "%s" is missing.',
                    [$prop],
                    'Admin.Design.Notification'
                );
            }
        }

        return !array_key_exists($themeName, $this->errors);
    }

    public function getRequiredProperties()
    {
        return [
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
        ];
    }

    private function hasRequiredFiles(Theme $theme)
    {
        $themeName = $theme->getName();
        $parentDir = realpath($this->appConfiguration->get('_PS_ALL_THEMES_DIR_') . $theme->get('parent')) . '/';
        $parentFile = false;

        foreach ($this->getRequiredFiles() as $file) {
            $childFile = $theme->getDirectory() . $file;
            if ($theme->get('parent')) {
                $parentFile = $parentDir . $file;
            }

            if (!file_exists($childFile) && !file_exists($parentFile)) {
                if (!array_key_exists($themeName, $this->errors)) {
                    $this->errors[$themeName] = [];
                }

                $this->errors[$themeName][] = $this->translator->trans('An error occurred. The template "%s" is missing.', [$file], 'Admin.Design.Notification');
            }
        }

        return !array_key_exists($themeName, $this->errors);
    }

    public function getRequiredFiles()
    {
        return [
            'preview.png',
            'config/theme.yml',
            'assets/js/theme.js',
            'assets/css/theme.css',
            // Templates
            'templates/_partials/form-fields.tpl',
            'templates/catalog/product.tpl',
            'templates/catalog/listing/product-list.tpl',
            'templates/checkout/cart.tpl',
            'templates/checkout/cart-empty.tpl',
            'templates/checkout/checkout.tpl',
            'templates/checkout/order-confirmation.tpl',
            'templates/cms/category.tpl',
            'templates/cms/page.tpl',
            'templates/cms/sitemap.tpl',
            'templates/cms/stores.tpl',
            'templates/contact.tpl',
            'templates/customer/address.tpl',
            'templates/customer/addresses.tpl',
            'templates/customer/authentication.tpl',
            'templates/customer/guest-tracking.tpl',
            'templates/customer/guest-login.tpl',
            'templates/customer/history.tpl',
            'templates/customer/identity.tpl',
            'templates/customer/my-account.tpl',
            'templates/customer/order-detail.tpl',
            'templates/customer/order-follow.tpl',
            'templates/customer/order-return.tpl',
            'templates/customer/order-slip.tpl',
            'templates/customer/registration.tpl',
            'templates/errors/404.tpl',
            'templates/errors/forbidden.tpl',
            'templates/index.tpl',
        ];
    }
}
