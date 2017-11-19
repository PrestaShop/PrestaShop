<?php
/**
 * 2007-2017 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
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
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2017 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */
namespace PrestaShopBundle\Service\Routing;

use Symfony\Bundle\FrameworkBundle\Routing\Router as BaseRouter;
use Symfony\Component\Security\Csrf\CsrfTokenManager;
use PrestaShopBundle\Service\DataProvider\UserProvider;

/**
 * We extends Symfony Router in order to add a token to each url.
 *
 * This is done for Security purposes.
 */
class Router extends BaseRouter
{
    private $userProvider;
    private $tokenManager;

    /**
     * {@inheritdoc}
     */
    public function generate($name, $parameters = array(), $referenceType = self::ABSOLUTE_PATH)
    {
        $url = parent::generate($name, $parameters, $referenceType);
        $token = $this->tokenManager->getToken($this->userProvider->getUsername())->getValue();

        return self::generateTokenizedUrl($url, $token);
    }

    public function setTokenManager(CsrfTokenManager $tokenManager)
    {
        $this->tokenManager = $tokenManager;
    }

    public function setUserProvider(UserProvider $userProvider)
    {
        $this->userProvider = $userProvider;
    }

    public static function generateTokenizedUrl($url, $token)
    {
        $components = parse_url($url);
        $baseUrl = (isset($components['path']) ? $components['path'] : '');
        $queryParams = array();
        if (isset($components['query'])) {
            $query = $components['query'];

            parse_str($query, $queryParams);
        }

        $queryParams['_token'] = $token;

        return $baseUrl.'?'.http_build_query($queryParams, '', '&');
    }
}
