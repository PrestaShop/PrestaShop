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
        // Browsers treat backslashes as forward slashes, so normalise them before reading the scheme.
        $normalisedUrl = str_replace('\\', '/', $url);

        // A url without an explicit scheme is relative (or protocol-relative) and is kept as before.
        if (!preg_match('#^\s*([a-z][a-z0-9+.\-]*)\s*:#i', $normalisedUrl, $matches)) {
            return true;
        }

        return in_array(strtolower(trim($matches[1])), ['http', 'https'], true);
    }
}
