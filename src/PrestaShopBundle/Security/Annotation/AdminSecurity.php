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

namespace PrestaShopBundle\Security\Annotation;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * Improves the existing Security annotation, adding:
 * `domain`: to translate the sent message using a PrestaShop domain;
 * `redirectRoute`: to select the route for redirection;
 * `url`: only available in 1.7.x, to redirect to legacy pages;.
 *
 * @Annotation
 */
class AdminSecurity extends Security
{
    /**
     * The translation domain for the message.
     *
     * @var string
     */
    protected $domain = 'Admin.Notifications.Error';

    /**
     * The route for the redirection.
     *
     * @todo: Once the onboarding page is migrated, set default to his route name.
     *
     * @var string
     */
    protected $redirectRoute;

    /**
     * @deprecated once the back office is migrated, rely only on route.
     * The url for the redirection
     *
     * @return string
     */
    protected $url = 'admin_domain';

    /**
     * The route params which are used together to generate the redirect route.
     *
     * @var array
     */
    protected $redirectQueryParamsToKeep = [];

    /**
     * @return string
     */
    public function getDomain()
    {
        return $this->domain;
    }

    /**
     * @param string $domain the translation domain name
     */
    public function setDomain($domain)
    {
        $this->domain = $domain;
    }

    /**
     * @return string
     */
    public function getRedirectRoute()
    {
        return $this->redirectRoute;
    }

    /**
     * @param string $redirectRoute the route used for redirection
     */
    public function setRedirectRoute($redirectRoute)
    {
        $this->redirectRoute = $redirectRoute;
    }

    /**
     * @return mixed
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @param string $url the url used for redirection
     */
    public function setUrl($url)
    {
        $this->url = $url;
    }

    /**
     * @return array
     */
    public function getRedirectQueryParamsToKeep()
    {
        return $this->redirectQueryParamsToKeep;
    }

    /**
     * @param array $redirectQueryParamsToKeep
     */
    public function setRedirectQueryParamsToKeep($redirectQueryParamsToKeep)
    {
        $this->redirectQueryParamsToKeep = $redirectQueryParamsToKeep;
    }
}
