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

namespace PrestaShopBundle\Service\Routing;

use PrestaShop\PrestaShop\Core\Feature\TokenInUrls;
use PrestaShopBundle\Security\Admin\UserTokenManager;
use Symfony\Bundle\FrameworkBundle\Routing\Router as BaseRouter;

/**
 * We extends Symfony Router in order to add a token to each url.
 *
 * This is done for Security purposes.
 */
class Router extends BaseRouter
{
    /**
     * @var UserTokenManager
     */
    private $userTokenManager;

    /**
     * {@inheritdoc}
     */
    public function generate($name, $parameters = [], $referenceType = self::ABSOLUTE_PATH): string
    {
        $url = parent::generate($name, $parameters, $referenceType);

        // For now, if we generate a _token and pass it in parameters, we must use it instead of use TokenManager.
        // todo: to be improved when UserProvider is also improved.
        // @see https://github.com/PrestaShop/PrestaShop/pull/32861
        if (TokenInUrls::isDisabled() || isset($parameters['_token'])) {
            return $url;
        }

        return self::generateTokenizedUrl($url, $this->userTokenManager->getSymfonyToken());
    }

    public function setUserTokenManager(UserTokenManager $userTokenManager)
    {
        $this->userTokenManager = $userTokenManager;
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

        return $url;
    }
}
