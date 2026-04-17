<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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

        $query = http_build_query($parameters, '', '&', PHP_QUERY_RFC3986);

        foreach ($parameters as $key => $value) {
            // Empty parameter that had no defined value must remain defined with no value (?action&otherAction not ?action=&otherAction=)
            if ($value === '' && !preg_match('/' . $key . '=[^&]*/', $parsedUrl['query'])) {
                $query = preg_replace('/' . $key . '=/', $key, $query);
            }
        }

        // Replace %5B%5D escaped brackets with actual brackets but keep their content
        $query = preg_replace('/\%5B([^\%5B\%5D]*?)\%5D/', '[$1]', $query);

        // Finally rebuild url with cleaned query parameters
        $parsedUrl['query'] = $query;

        return http_build_url($parsedUrl);
    }
}
