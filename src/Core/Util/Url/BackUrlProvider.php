<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Util\Url;

use Symfony\Component\HttpFoundation\Request;

/**
 * gets back url.
 */
class BackUrlProvider
{
    /**
     * @param Request $request
     *
     * @return string
     */
    public function getBackUrl(Request $request)
    {
        $backUrl = rawurldecode($request->query->get('back', ''));

        // The back url is later followed as a redirection target and as a link href, so a value
        // pointing somewhere else than the current host is dropped to avoid an open redirection.
        if ('' === $backUrl || !$this->belongsToCurrentHost($backUrl, $request)) {
            return '';
        }

        return $backUrl;
    }

    /**
     * @param string $url
     * @param Request $request
     *
     * @return bool
     */
    private function belongsToCurrentHost(string $url, Request $request): bool
    {
        // Browsers treat backslashes as forward slashes, so normalise them first otherwise a value
        // such as "/\evil.com" would smuggle an external host past the checks below.
        $normalisedUrl = str_replace('\\', '/', $url);

        // Protocol-relative urls ("//host") resolve to an external host once the browser prepends
        // the current scheme.
        if (str_starts_with($normalisedUrl, '//')) {
            return false;
        }

        // Only http(s) urls are accepted, which also rules out javascript: and data: schemes.
        if (preg_match('#^([a-z][a-z0-9+.\-]*):#i', $normalisedUrl, $matches)
            && !in_array(strtolower($matches[1]), ['http', 'https'], true)
        ) {
            return false;
        }

        $parsedUrl = parse_url($normalisedUrl);
        if (false === $parsedUrl) {
            return false;
        }

        // Relative urls have no host and stay on the current shop, absolute ones must target it.
        return empty($parsedUrl['host']) || 0 === strcasecmp($parsedUrl['host'], (string) $request->getHost());
    }
}
