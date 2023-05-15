<?php

namespace PrestaShopBundle\Routing;

use PrestaShop\PrestaShop\Core\Domain\Configuration\ShopConfigurationInterface;
use PrestaShopBundle\Service\DataProvider\UserProvider;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

/**
 * Appends the token at the end of the URL if the option is enabled.
 */
class Router implements RouterInterface
{
    private array $tokens = [];

    public function __construct(
        private readonly ShopConfigurationInterface $configuration,
        private readonly RouterInterface $generator,
        private readonly CsrfTokenManagerInterface $tokenManager,
        private readonly UserProvider $userProvider
    ) {
    }

    public function setContext(RequestContext $context)
    {
        return $this->generator->setContext($context);
    }

    public function getContext(): RequestContext
    {
        return $this->generator->getContext();
    }

    public function generate($name, $parameters = [], $referenceType = self::ABSOLUTE_PATH): string
    {
        $username = $this->userProvider->getUsername();
        // Do not generate token each time we want to generate a route for a user
        if (!isset($this->tokens[$username])) {
            $this->tokens[$username] = $this->tokenManager->getToken($username)->getValue();
        }

        $token = $this->tokens[$username];

        if ($this->hasTokenInUrls()) {
            $parameters['_token'] = $parameters['_token'] ?? $token;
        }

        return $this->generator->generate($name, $parameters, $referenceType);
    }

    private function hasTokenInUrls(): bool
    {
        return $this->configuration->getBoolean('PS_SECURITY_TOKEN');
    }

    public function getRouteCollection()
    {
        return $this->generator->getRouteCollection();
    }

    public function match($pathinfo)
    {
        return $this->generator->match($pathinfo);
    }
}
