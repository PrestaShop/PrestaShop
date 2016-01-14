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
namespace PrestaShop\PrestaShop\Core\Addon\Theme;

class ThemeChecker
{
    private $theme;

    public function setTheme(Theme $theme)
    {
        $this->theme = $theme;
        return $this;
    }

    public function isValid()
    {
        $valid = true;
        $valid &= $this->hasRequiredFiles();
        $valid &= $this->hasMiniumProperties();

        return (bool)$valid;
    }

    public function hasMiniumProperties()
    {
        foreach ($this->getMinimumProperties() as $prop) {
            $p = explode('.', $prop);

            if (!isset($this->theme->{$p[0]})) {
                return false;
            }

            $var = $this->theme->{$p[0]};
            for ($i=1; $i < count($p); $i++) {
                if (!isset($var[$p[$i]])) {
                    return false;
                }
                $var = $var[$p[$i]];
            }
        }

        return true;
    }

    public function getMinimumProperties()
    {
        return [
            'name',
            'version',
            'meta.compatibility.from',
            'meta.available_layouts',
        ];
    }

    public function hasRequiredFiles()
    {
        foreach ($this->getRequiredFiles() as $file) {
            if (!file_exists($this->theme->directory.$file)) {
                return false;
            }
        }

        return true;
    }

    public function getRequiredFiles()
    {
        return [
            'preview.png',
            'config/theme.yml',
            'assets/js/theme.js',
            'assets/css/theme.css',
            'templates/page.tpl',
            'templates/catalog/product.tpl',
            'templates/catalog/product-miniature.tpl',
            'templates/checkout/cart.tpl',
            'templates/checkout/checkout.tpl',
            'templates/_partials/head.tpl',
            'templates/_partials/header.tpl',
            'templates/_partials/notifications.tpl',
            'templates/_partials/footer.tpl',
        ];
    }
}
