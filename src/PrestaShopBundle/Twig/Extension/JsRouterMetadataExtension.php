<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Twig\Extension;

use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * Provides data needed for Javascript router component
 */
class JsRouterMetadataExtension extends AbstractExtension
{
    public function __construct(
        private readonly RequestStack $requestStack,
        private readonly CsrfTokenManagerInterface $tokenManager,
        private readonly Security $security,
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('js_router_metadata', [$this, 'getJsRouterMetadata']),
        ];
    }

    /**
     * Get base url and security token used for javascript router component.
     *
     * @return array
     */
    public function getJsRouterMetadata(): array
    {
        return [
            // base url for javascript router
            'base_url' => $this->requestStack->getCurrentRequest()->getBaseUrl(),
            // security token for javascript router
            'token' => $this->tokenManager->getToken($this->security->getUser()->getUserIdentifier())->getValue(),
        ];
    }
}
