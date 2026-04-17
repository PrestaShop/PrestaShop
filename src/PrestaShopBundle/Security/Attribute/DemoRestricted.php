<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Security\Attribute;

use Attribute;

/**
 * Forbid access to the page if Demonstration mode is enabled.
 */
#[Attribute(Attribute::TARGET_METHOD)]
class DemoRestricted
{
    public function __construct(
        /**
         * The route for the redirection.
         */
        private ?string $redirectRoute = null,
        /**
         * The message of the exception.
         */
        private string $message = 'This functionality has been disabled.',
        /**
         * The translation domain for the message.
         */
        private string $domain = 'Admin.Notifications.Error',
        /**
         * The route params which are used together to generate the redirect route.
         */
        private array $redirectQueryParamsToKeep = []
    ) {
    }

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
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @param string $message the message displayed after redirection
     */
    public function setMessage($message)
    {
        $this->message = $message;
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
     * Returns the alias name for an annotated configuration.
     *
     * @return string
     */
    public function getAliasName()
    {
        return 'demo_restricted';
    }

    /**
     * Returns whether multiple annotations of this type are allowed.
     *
     * @return bool
     */
    public function allowArray()
    {
        return true;
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
