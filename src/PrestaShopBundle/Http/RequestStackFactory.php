<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\Http;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * This factory is used to initialize the request in frontend environement
 */
class RequestStackFactory
{
    public static function BuildRequestStack(): RequestStack
    {
        $request = Request::createFromGlobals();
        Request::setTrustedProxies([], Request::HEADER_X_FORWARDED_FOR | Request::HEADER_X_FORWARDED_HOST | Request::HEADER_X_FORWARDED_PORT | Request::HEADER_X_FORWARDED_PROTO);

        $requestStack = new RequestStack();
        $requestStack->push($request);

        return $requestStack;
    }
}
