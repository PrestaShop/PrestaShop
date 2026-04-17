<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Twig\Extension;

use PrestaShop\PrestaShop\Core\Util\Url\BackUrlProvider;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * This class adds a function to twig template which points to back url if such is found in current request.
 */
class PathWithBackUrlExtension extends AbstractExtension
{
    public function __construct(
        private readonly UrlGeneratorInterface $urlGenerator,
        private readonly BackUrlProvider $backUrlProvider,
        private readonly RequestStack $requestStack
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction(
                'pathWithBackUrl',
                [$this, 'getPathWithBackUrl']
            ),
        ];
    }

    /**
     * Gets original path or back url path.
     *
     * @param string $name - route name
     * @param array $parameters - route parameters
     * @param bool $relative
     *
     * @return string
     */
    public function getPathWithBackUrl(string $name, array $parameters = [], bool $relative = false): string
    {
        $fallbackPath = $this->urlGenerator->generate($name, $parameters, $relative ? UrlGeneratorInterface::RELATIVE_PATH : UrlGeneratorInterface::ABSOLUTE_PATH);

        $request = $this->requestStack->getCurrentRequest();

        if (null === $request) {
            return $fallbackPath;
        }

        $backUrl = $this->backUrlProvider->getBackUrl($request);

        if (!$backUrl) {
            return $fallbackPath;
        }

        return $backUrl;
    }
}
