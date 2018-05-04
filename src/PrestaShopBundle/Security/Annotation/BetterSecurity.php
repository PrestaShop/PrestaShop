<?php
/**
 * 2007-2018 PrestaShop
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
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */
namespace PrestaShopBundle\Security\Annotation;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * Improves the existing Security annotation, adding:
 * `domain`: to translate the sent message using a PrestaShop domain;
 * `route`: to select the route for redirection;
 *
 * @Annotation
 */
class BetterSecurity extends Security
{
    /**
     * The translation domain for the message.
     *
     * @var string
     */
    protected $domain = 'Admin.Notifications.Error';

    /**
     * The route for the redirection.
     * @todo: Once the onboarding page is migrated, set default to his route name.
     *
     * @var string
     */
    protected $route;

    /**
     * @deprecated 1.8.x once the back office is migrated, rely only on route.
     * The url for the redirection.
     *
     * @return string
     */
    protected $url;

    /**
     * @return string
     */
    public function getDomain()
    {
        return $this->domain;
    }

    /**
     * @param $domain
     */
    public function setDomain( $domain)
    {
        $this->domain = $domain;
    }

    /**
     * @return string
     */
    public function getRoute()
    {
        return $this->route;
    }

    /**
     * @param $route
     */
    public function setRoute( $route)
    {
        $this->route = $route;
    }

    /**
     * @return mixed
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @param $url
     */
    public function setUrl( $url)
    {
        $this->url = $url;
    }
}
