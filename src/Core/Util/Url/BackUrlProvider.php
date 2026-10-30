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

        // The back url is also rendered as the cancel-button href, so a javascript: or data: value
        // would become a clickable script link. External http(s) targets stay supported, only a non
        // http(s) scheme is dropped, in which case both consumers fall back to their safe default.
        if ('' !== $backUrl && !$this->hasHttpScheme($backUrl)) {
            return '';
        }

        return $backUrl;
    }

    /**
     * @param string $url
     *
     * @return bool
     */
    private function hasHttpScheme(string $url): bool
    {
        // Browsers strip ASCII tab, LF and CR from anywhere in a URL and trim leading C0 controls
        // before reading the scheme, and treat backslashes as forward slashes. Normalise the same
        // way first, or "java\tscript:" passes as relative here and still runs in the browser.
        $normalisedUrl = str_replace(['\\', "\t", "\n", "\r"], ['/', '', '', ''], $url);
        $normalisedUrl = ltrim($normalisedUrl, "\x00..\x20");

        $matched = preg_match('#^([a-z][a-z0-9+.\-]*):#i', $normalisedUrl, $matches);
        if (false === $matched) {
            // A failed match says nothing about the value, so refuse instead of letting it through.
            return false;
        }
        if (0 === $matched) {
            return true;
        }

        return in_array(strtolower($matches[1]), ['http', 'https'], true);
    }
}
