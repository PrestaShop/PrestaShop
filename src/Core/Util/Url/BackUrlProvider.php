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
        $backUrl = $request->query->get('back', '');

        return rawurldecode($backUrl);
    }
}
