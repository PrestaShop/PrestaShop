<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Country\AddressFormat;

/**
 * Validates the country address-format string against the same rules the legacy widget enforces:
 * unknown property/class tokens, forbidden classes, duplicate tokens, missing required fields.
 *
 * Both the Symfony constraint and the CQRS handler depend on this interface so the validation
 * surface stays consistent regardless of entry point.
 */
interface AddressFormatCheckerInterface
{
    /**
     * @return string[] list of (already translated) error messages, empty when valid
     */
    public function validate(string $format): array;
}
