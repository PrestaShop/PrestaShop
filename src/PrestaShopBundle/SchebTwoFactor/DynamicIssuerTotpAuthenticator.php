<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\SchebTwoFactor;

use PrestaShop\PrestaShop\Adapter\Configuration;
use Scheb\TwoFactorBundle\Model\Totp\TwoFactorInterface;
use Scheb\TwoFactorBundle\Security\TwoFactor\Provider\Totp\TotpAuthenticatorInterface;

/**
 * Decorator that customizes the TOTP issuer to use the shop name.
 * This makes the entry in authenticator apps display the actual shop name
 * instead of a generic "PrestaShop" label.
 */
final class DynamicIssuerTotpAuthenticator implements TotpAuthenticatorInterface
{
    public function __construct(
        private readonly TotpAuthenticatorInterface $inner,
        private readonly Configuration $configuration,
    ) {
    }

    public function checkCode(TwoFactorInterface $user, string $code): bool
    {
        return $this->inner->checkCode($user, $code);
    }

    public function getQRContent(TwoFactorInterface $user): string
    {
        $uri = $this->inner->getQRContent($user);
        $shopName = $this->configuration->get('PS_SHOP_NAME');

        // Replace the issuer in the otpauth URI with the shop name
        // URI format: otpauth://totp/label?secret=...&issuer=...
        $encodedShopName = rawurlencode($shopName);

        // Replace existing issuer parameter
        if (preg_match('/([?&])issuer=[^&]*/', $uri)) {
            $uri = preg_replace('/([?&])issuer=[^&]*/', '$1issuer=' . $encodedShopName, $uri);
        } else {
            // Add issuer parameter if not present
            $uri .= (str_contains($uri, '?') ? '&' : '?') . 'issuer=' . $encodedShopName;
        }

        // Also update the label prefix (before the colon) to match the issuer
        // Format: otpauth://totp/Issuer:username@server?...
        $uri = preg_replace(
            '#^(otpauth://totp/)[^:]+:#',
            '$1' . $encodedShopName . ':',
            $uri
        );

        return $uri;
    }

    public function generateSecret(): string
    {
        return $this->inner->generateSecret();
    }
}
