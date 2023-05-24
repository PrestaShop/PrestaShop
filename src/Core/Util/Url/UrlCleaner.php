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

namespace PrestaShop\PrestaShop\Core\Util\Url;

class UrlCleaner
{
    public static function cleanUrl(string $url, array $removedParams): string
    {
        $parsedUrl = parse_url($url);
        $parameters = [];
        if (isset($parsedUrl['query'])) {
            parse_str($parsedUrl['query'], $parameters);
        }

        foreach ($removedParams as $removedParam) {
            unset($parameters[$removedParam]);
        }

        $query = http_build_query($parameters, '', '&');

        // Replace %5B%5D escaped brackets with actual brackets but keep their content
        $query = preg_replace('/\%5B([^\%5B\%5D]*?)\%5D/', '[$1]', $query);
        $parsedUrl['query'] = $query;

        return http_build_url($parsedUrl);
    }
}
