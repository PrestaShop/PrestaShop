<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
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
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Context;

use Language;
use Media;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Traversable;

/**
 * This class ensures compatibility with the context controller in pages migrated to Symfony.
 * It encompasses the majority of public fields found in a legacy controller.
 */
class LegacyControllerContext
{
    /**
     * List of CSS files.
     *
     * @var string[]
     */
    public array $css_files = [];

    /**
     * List of JavaScript files.
     *
     * @var string[]
     */
    public array $js_files = [];

    /**
     * Controller name alias kept for backward compatibility.
     *
     * @var string
     */
    public readonly string $php_self;

    /**
     * Error messages displayed after refresh
     *
     * @var array<string|int, string|bool>
     */
    public array $errors = [];

    /**
     * Warning messages displayed after refresh
     *
     * @var array<string|int, string|bool>
     */
    public array $warnings = [];

    /**
     * Information messages displayed after refresh
     *
     * @var array<string|int, string|bool>
     */
    public array $informations = [];

    /**
     * Confirmation/success messages displayed after refresh
     *
     * @var array<string|int, string|bool>
     */
    public array $confirmations = [];

    /**
     * Image type
     *
     * @var string
     */
    public string $imageType = 'jpg';

    /**
     * Array description of buttons to add in the header toolbar
     *
     * @var array|Traversable
     */
    public array|Traversable $page_header_toolbar_btn = [];

    protected array $languages = [];

    /**
     * @param ContainerInterface $container Dependency container
     * @param string $controller_name Current controller name without suffix
     * @param string $controller_type Controller type. Possible values: 'front', 'modulefront', 'admin', 'moduleadmin'.
     * @param int $multishop_context Allowed multi shop contexts Possible values: Byte addition of ShopConstraint::ALL_SHOPS | ShopConstraint::SHOP_GROUP | ShopConstraint::SHOP
     * @param string|null $className Legacy ObjectModel associated to the controller (if possible)
     * @param int $id Tab ID
     * @param string|null $token Legacy security token
     * @param string $override_folder
     * @param string $currentIndex Legacy current index built like a legacy URL based on controller name
     */
    public function __construct(
        protected readonly ContainerInterface $container,
        public readonly string $controller_name,
        public readonly string $controller_type,
        public readonly int $multishop_context,
        public readonly ?string $className,
        public readonly int $id,
        public readonly ?string $token,
        public readonly string $override_folder,
        public readonly string $currentIndex,
        public readonly string $table,
        protected readonly int $employeeLanguageId,
    ) {
        $this->php_self = $this->controller_name;
    }

    public function addCSS(array|string $css_uri, string $css_media_type = 'all', ?int $offset = null, bool $check_path = true): void
    {
        if (!is_array($css_uri)) {
            $css_uri = [$css_uri];
        }

        foreach ($css_uri as $css_file => $media) {
            if (is_string($css_file) && strlen($css_file) > 1) {
                if ($check_path) {
                    $css_path = Media::getCSSPath($css_file, $media);
                } else {
                    $css_path = [$css_file => $media];
                }
            } else {
                if ($check_path) {
                    $css_path = Media::getCSSPath($media, $css_media_type);
                } else {
                    $css_path = [$media => $css_media_type];
                }
            }

            $key = is_array($css_path) ? key($css_path) : $css_path;
            if ($css_path && (!isset($this->css_files[$key]) || ($this->css_files[$key] != reset($css_path)))) {
                $size = count($this->css_files);
                if ($offset === null || $offset > $size || $offset < 0 || !is_numeric($offset)) {
                    $offset = $size;
                }

                $this->css_files = array_merge(array_slice($this->css_files, 0, $offset), $css_path, array_slice($this->css_files, $offset));
            }
        }
    }

    public function addJS(array|string $js_uri, bool $check_path = true): void
    {
        if (!is_array($js_uri)) {
            $js_uri = [$js_uri];
        }

        foreach ($js_uri as $js_file) {
            $js_file = explode('?', $js_file);
            $version = '';
            if (isset($js_file[1]) && $js_file[1]) {
                $version = $js_file[1];
            }
            $js_path = $js_file = $js_file[0];
            if ($check_path) {
                $js_path = Media::getJSPath($js_file);
            }

            if ($js_path && !in_array($js_path, $this->js_files)) {
                $this->js_files[] = $js_path . ($version ? '?' . $version : '');
            }
        }
    }

    /**
     * Adds jQuery UI component(s) to queued JS file list.
     */
    public function addJqueryUI(string|array $component, string $theme = 'base', bool $checkDependencies = true): void
    {
        if (!is_array($component)) {
            $component = [$component];
        }

        foreach ($component as $ui) {
            $ui_path = Media::getJqueryUIPath($ui, $theme, $checkDependencies);
            $this->addCSS($ui_path['css'], 'all');
            $this->addJS($ui_path['js'], false);
        }
    }

    /**
     * Adds jQuery plugin(s) to queued JS file list.
     */
    public function addJqueryPlugin(string|array $name, ?string $folder = null, bool $css = true): void
    {
        if (!is_array($name)) {
            $name = [$name];
        }

        foreach ($name as $plugin) {
            $plugin_path = Media::getJqueryPluginPath($plugin, $folder);

            if (!empty($plugin_path['js'])) {
                $this->addJS($plugin_path['js'], false);
            }
            if ($css && !empty($plugin_path['css'])) {
                $this->addCSS(key($plugin_path['css']), 'all', null, false);
            }
        }
    }

    public function getLanguages(): array
    {
        if (!empty($this->languages)) {
            return $this->languages;
        }

        $this->languages = Language::getLanguages(false);
        foreach ($this->languages as $k => $language) {
            $this->languages[$k]['is_default'] = (int) ($language['id_lang'] == $this->employeeLanguageId);
        }

        return $this->languages;
    }

    public function getContainer(): ContainerInterface
    {
        return $this->container;
    }
}
