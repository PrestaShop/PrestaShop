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
                [$this, 'getLegacyPath']
            ),
            new TwigFunction(
                'legacy_admin_token',
                [$this, 'getLegacyAdminToken']
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
