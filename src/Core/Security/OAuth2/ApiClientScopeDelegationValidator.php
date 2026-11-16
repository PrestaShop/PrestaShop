<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Security\OAuth2;

use PrestaShop\PrestaShop\Core\Context\ApiClientContext;
use PrestaShop\PrestaShop\Core\Domain\ApiClient\Exception\ApiClientConstraintException;

/**
 * Checks that an API client only grants the scopes it holds itself.
 *
 * The InstalledApiResourceScope constraint checks that the requested scopes exist,
 * this class checks that the authenticated API client is allowed to grant them.
 */
final class ApiClientScopeDelegationValidator
{
    /**
     * Scopes implicitly granted to every authenticated API client at token issuance.
     * They are never stored on the client and must not be treated as a delegation attempt.
     */
    private const IMPLICIT_SCOPES = ['is_authenticated'];

    public function __construct(
        private readonly ApiClientContext $apiClientContext,
    ) {
    }

    /**
     * @param string[] $requestedScopes the scopes the caller wants to assign to an API client
     *
     * @throws ApiClientConstraintException when the caller tries to grant scopes it does not hold
     */
    public function assertCanGrant(array $requestedScopes): void
    {
        $callerApiClient = $this->apiClientContext->getApiClient();

        // When the caller is not an API client (e.g. an employee acting through the Back Office),
        // no delegation restriction applies: they are already gated by their employee permissions.
        // This also preserves the bootstrap case where the very first API client is created from the BO.
        if (null === $callerApiClient) {
            return;
        }

        $allowedScopes = array_merge($callerApiClient->getScopes(), self::IMPLICIT_SCOPES);
        $notAllowedScopes = array_values(array_diff($requestedScopes, $allowedScopes));

        if (!empty($notAllowedScopes)) {
            throw new ApiClientConstraintException(
                sprintf(
                    'Cannot grant scopes the authenticated client does not hold: %s',
                    implode(', ', $notAllowedScopes)
                ),
                ApiClientConstraintException::INVALID_SCOPES
            );
        }
    }
}
