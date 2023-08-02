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

namespace PrestaShopBundle\Twig\Component;

use Link;
use Media;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;
use Tools;

#[AsTwigComponent(template: '@PrestaShop/Admin/Component/Layout/header.html.twig')]
class Header
{
    public Link $link;
    public ?string $viewport_scale;
    public string $img_dir;
    public string $meta_title;
    public string $shop_name;
    public bool $display_header_javascript;

    public string $controller_name;

    public string $iso_user;
    public string $lang_is_rtl;
    public string $full_language_code;
    public string $full_cldr_language_code;
    public string $country_iso_code;
    public string $ps_version;
    public string $round_mode;
    public ?string $shop_context;
    public string $token;
    public string $currentIndex;
    public string $default_language;

    public array $css_files;
    public array $js_files;
    public array $js_inline;
    public ?string $displayBackOfficeHeader;

    public function getEmployeeToken(): string
    {
        return Tools::getAdminToken('AdminEmployees');
    }

    public function getJsDef(): array
    {
        return Media::getJsDef();
    }
}
