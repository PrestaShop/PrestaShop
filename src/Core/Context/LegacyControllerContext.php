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

use Media;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Traversable;

/**
 * This class ensures compatibility with the context controller in pages migrated to Symfony.
 * It encompasses the majority of public fields found in a legacy controller.
 */
class LegacyControllerContext
{
    public ?string $className;

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

    // Controller type. Possible values: 'front', 'modulefront', 'admin', 'moduleadmin'.
    public string $controller_type;

    // Controller name.
    public string $php_self;

    /**
     * Errors displayed after post processing
     *
     * @var array<string|int, string|bool>
     */
    public array $errors = [];

    public array $warnings = [];

    public array $informations = [];

    public array $confirmations = [];

    public string $currentIndex;

    public int $id = -1;

    // Security token
    public ?string $token;

    public string $override_folder;

    // Image type
    public string $imageType = 'jpg';

    // Current controller name without suffix
    public string $controller_name;

    public int $multishop_context = -1;

    // Bootstrap variable
    public array|Traversable $page_header_toolbar_btn = [];

    // Dependency container
    protected ContainerInterface $container;

    public function __construct(
        ContainerInterface $container,
        string $controller_name,
        string $controller_type,
        string $php_self,
        int $multishop_context,
        ?string $className,
        int $id,
        ?string $token,
        string $override_folder,
    ) {
        $this->container = $container;
        $this->controller_type = $controller_type;
        $this->php_self = $php_self;
        $this->controller_name = $controller_name;
        $this->multishop_context = $multishop_context;
        $this->className = $className;
        $this->id = $id;
        $this->token = $token;
        $this->override_folder = $override_folder;
    }

    public function addCSS($css_uri, $css_media_type = 'all', $offset = null, $check_path = true): void
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

    public function addJS($js_uri, $check_path = true): void
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

    public function getContainer(): ContainerInterface
    {
        return $this->container;
    }
}
