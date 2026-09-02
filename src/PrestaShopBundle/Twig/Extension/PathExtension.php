<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Twig\Extension;

use PrestaShop\PrestaShop\Adapter\LegacyContext;
use PrestaShop\PrestaShop\Core\Security\Hashing;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * This class adds a function to twig template which points to back url if such is found in current request.
 */
class PathExtension extends AbstractExtension
{
    public function __construct(
        private readonly LegacyContext $context,
        private readonly Hashing $hashing,
        private readonly string $cookieKey
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction(
                'legacy_path',
                [$this, 'getLegacyPath'],
                ['is_safe' => ['html']]
            ),
            new TwigFunction(
                'legacy_admin_token',
                [$this, 'getLegacyAdminToken'],
                ['is_safe' => ['html']]
            ),
        ];
    }

    /**
     * Get path for legacy link.
     *
     * @param string $controllerName
     * @param array $parameters
     *
     * @return string
     */
    public function getLegacyPath(string $controllerName, array $parameters = []): string
    {
        return $this->context->getAdminLink($controllerName, extraParams: $parameters);
    }

    /**
     * Get token for legacy controller, this method mimics the same behaviour as Tools::getAdminToken.
     *
     * @param string $controllerName
     *
     * @return string
     */
    public function getLegacyAdminToken(string $controllerName): string
    {
        return $this->hashing->hash($controllerName, $this->cookieKey);
    }
}
