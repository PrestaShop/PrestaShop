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

declare(strict_types=1);

namespace Tests\Integration\Utility;

use Symfony\Component\Security\Csrf\TokenStorage\ClearableTokenStorageInterface;

/**
 * Custom implementation of CSRF token storage for the test environment.
 * By default, Symfony stores tokens in the session, which is not available outside the context of a request.
 * Overriding the CSRFTokenStorage allows us to use the router to generate URLs even outside/before a request
 * context, this is due to our Router overriding system that relies on the token and therefore the session.
 */
class CsrfTokenStorage implements ClearableTokenStorageInterface
{
    /** @var string[] */
    private array $tokens = ['test@prestashop.com' => 'fakeTestCsrfToken'];

    public function clear(): void
    {
        $this->tokens = [];
    }

    public function getToken(string $tokenId): string
    {
        return $this->tokens[$tokenId];
    }

    public function setToken(string $tokenId, string $token): void
    {
        $this->tokens[$tokenId] = $token;
    }

    public function removeToken(string $tokenId): ?string
    {
        $token = $this->tokens[$tokenId];

        unset($this->tokens[$tokenId]);

        return $token;
    }

    public function hasToken(string $tokenId): bool
    {
        return array_key_exists($tokenId, $this->tokens);
    }
}
