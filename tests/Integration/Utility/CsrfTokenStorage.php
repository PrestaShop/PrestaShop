<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
