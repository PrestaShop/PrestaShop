<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Country\AddressFormat;

use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Pure Core implementation of {@see AddressFormatCheckerInterface}.
 *
 * Validates an address-format string against the picker's known object/field
 * surface (provided by {@see AddressFormatFieldsProviderInterface}) without
 * touching the legacy AddressFormat ObjectModel. Encapsulates the same rules
 * the legacy widget enforced:
 *
 *   - tokens are split on non-word/non-colon characters
 *   - bare tokens must name a valid Address field
 *   - prefixed `Object:field` tokens must name an exposed picker class with a
 *     known field on it
 *   - duplicate tokens are rejected
 *   - all required fields must be present (bare entries match Address tokens)
 *
 * Errors are translated through the Symfony TranslatorInterface so unit tests
 * and the form layer get human-readable strings without booting the legacy
 * Context.
 */
final class AddressFormatChecker implements AddressFormatCheckerInterface
{
    /**
     * Mirrors the legacy AddressFormat::_CLEANING_REGEX_ — splits a line on
     * any character that isn't a word char, colon, or underscore.
     */
    private const TOKEN_SPLIT_REGEX = '#([^\w:_]+)#i';

    private const TRANSLATION_DOMAIN = 'Admin.Notifications.Error';

    public function __construct(
        private readonly AddressFormatFieldsProviderInterface $fieldsProvider,
        private readonly TranslatorInterface $translator,
    ) {
    }

    public function validate(string $format): array
    {
        $errors = [];
        $usedTokens = [];
        $placedKeys = [];

        foreach (explode("\n", $format) as $line) {
            foreach ($this->extractTokens($line) as $token) {
                if (in_array($token, $usedTokens, true)) {
                    $errors[] = $this->translator->trans(
                        'This key has already been used: %key%.',
                        ['%key%' => $token],
                        self::TRANSLATION_DOMAIN
                    );
                    continue;
                }
                $usedTokens[] = $token;

                $placement = $this->resolveTokenPlacement($token, $errors);
                if (null !== $placement) {
                    $placedKeys[] = $placement;
                }
            }
        }

        foreach ($this->findMissingRequiredFields($placedKeys) as $missing) {
            $errors[] = $this->translator->trans(
                'The %field% field is required.',
                ['%field%' => $missing],
                self::TRANSLATION_DOMAIN
            );
        }

        return $errors;
    }

    /**
     * @return list<string>
     */
    private function extractTokens(string $line): array
    {
        $tokens = preg_split(self::TOKEN_SPLIT_REGEX, $line, -1, PREG_SPLIT_NO_EMPTY);

        return false === $tokens ? [] : array_values($tokens);
    }

    /**
     * @param list<string> $errors collected error messages, mutated on validation failure
     *
     * @return string|null Canonical "Object:field" key for required-fields tracking,
     *                     or null when the token failed validation
     */
    private function resolveTokenPlacement(string $token, array &$errors): ?string
    {
        if (str_contains($token, ':')) {
            $parts = explode(':', $token);
            if (2 !== count($parts) || '' === $parts[0] || '' === $parts[1]) {
                $errors[] = $this->translator->trans(
                    'Syntax error with this pattern: %pattern%.',
                    ['%pattern%' => $token],
                    self::TRANSLATION_DOMAIN
                );

                return null;
            }
            $class = ucfirst($parts[0]);
            $field = strtolower($parts[1]);

            if (!in_array($class, $this->fieldsProvider->getPickerClasses(), true)) {
                $errors[] = $this->translator->trans(
                    'This object is not allowed: %name%.',
                    ['%name%' => $class],
                    self::TRANSLATION_DOMAIN
                );

                return null;
            }
            if (!in_array($field, $this->fieldsProvider->getFieldsForClass($class), true)) {
                $errors[] = $this->translator->trans(
                    'The field %field% does not exist on %class%.',
                    ['%field%' => $field, '%class%' => $class],
                    self::TRANSLATION_DOMAIN
                );

                return null;
            }

            return $class . ':' . $field;
        }

        // Bare token: must be an Address field.
        $field = strtolower($token);
        if (!in_array($field, $this->fieldsProvider->getFieldsForClass('Address'), true)) {
            $errors[] = $this->translator->trans(
                'This field is not a valid address property: %name%.',
                ['%name%' => $token],
                self::TRANSLATION_DOMAIN
            );

            return null;
        }

        return 'Address:' . $field;
    }

    /**
     * @param list<string> $placedKeys canonical "Object:field" keys derived from the format
     *
     * @return list<string> required-fields entries that aren't covered by any placed token
     */
    private function findMissingRequiredFields(array $placedKeys): array
    {
        $required = $this->fieldsProvider->getRequiredFields();
        $missing = [];
        foreach ($required as $req) {
            $needed = str_contains($req, ':') ? $req : 'Address:' . $req;
            if (!in_array($needed, $placedKeys, true)) {
                $missing[] = $req;
            }
        }

        return $missing;
    }
}
