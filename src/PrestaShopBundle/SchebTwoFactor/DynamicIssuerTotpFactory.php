<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\SchebTwoFactor;

use PrestaShop\PrestaShop\Adapter\Configuration;
use Scheb\TwoFactorBundle\Security\TwoFactor\Provider\Totp\TotpFactory;

final class DynamicIssuerTotpFactory extends TotpFactory
{
    public function __construct(
        ?string $server,
        ?string $issuer,
        array $customParameters,
        Configuration $configuration
    ) {
        $customParameters['image'] = _PS_IMG_DIR_ . $configuration->get('PS_LOGO');
        $issuer = $configuration->get('PS_SHOP_NAME');

        parent::__construct($server, $issuer, $customParameters);
    }
}
