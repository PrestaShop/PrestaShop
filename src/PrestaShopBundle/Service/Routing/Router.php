<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Service\Routing;

use PrestaShop\PrestaShop\Core\Feature\TokenInUrls;
use PrestaShopBundle\Routing\AnonymousRouteProvider;
use PrestaShopBundle\Security\Admin\UserTokenManager;
use Symfony\Bundle\FrameworkBundle\Routing\Router as BaseRouter;

/**
 * We extends Symfony Router in order to add a token to each url.
 *
 * This is done for Security purposes.
 */
class Router extends BaseRouter
{
    private UserTokenManager $userTokenManager;

    private AnonymousRouteProvider $anonymousRouteProvider;

    /**
     * {@inheritdoc}
     */
    public function generate($name, $parameters = [], $referenceType = self::ABSOLUTE_PATH): string
    {
        $url = parent::generate($name, $parameters, $referenceType);
        if (TokenInUrls::isDisabled() || $this->anonymousRouteProvider->isRouteAnonymous($name)) {
            return $url;
        }

        return self::generateTokenizedUrl($url, $this->userTokenManager->getSymfonyToken());
    }

    public function setUserTokenManager(UserTokenManager $userTokenManager): void
    {
        $this->userTokenManager = $userTokenManager;
    }

    public function setAnonymousRouteProvider(AnonymousRouteProvider $anonymousRouteProvider): void
    {
        $this->anonymousRouteProvider = $anonymousRouteProvider;
    }

    public static function generateTokenizedUrl($url, $token)
    {
        $components = parse_url($url);
        $baseUrl = (isset($components['path']) ? $components['path'] : '');
        $queryParams = [];
        if (isset($components['query'])) {
            $query = $components['query'];

            parse_str($query, $queryParams);
        }

        $queryParams['_token'] = $token;

        $url = $baseUrl . '?' . http_build_query($queryParams, '', '&');
        if (isset($components['fragment']) && $components['fragment'] !== '') {
            /* This copy-paste from Symfony's UrlGenerator */
            $url .= '#' . strtr(rawurlencode($components['fragment']), ['%2F' => '/', '%3F' => '?']);
        }

        // Keep absolute urls absolute
        if (!empty($components['scheme']) && !empty($components['host'])) {
            $baseHost = $components['scheme'] . '://' . $components['host'];
            if (!empty($components['port'])) {
                $baseHost .= ':' . $components['port'];
            }
            $url = $baseHost . $url;
        }

        return $url;
    }
}
