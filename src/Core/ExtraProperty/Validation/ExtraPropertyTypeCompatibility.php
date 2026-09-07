<?php

/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\ExtraProperty\Validation;

use PrestaShop\PrestaShop\Core\ExtraProperty\Definition\ExtraPropertyType;
use Symfony\Component\Validator\Constraint;

/**
 * Symfony constraint form of the implicit type-compatibility rule
 * (ExtraPropertyValidator::isValueCompatible()) — the same rule-set guarding default
 * values at registration and every validateValue() call (ObjectModel, Admin API).
 *
 * Auto-attached by ExtraPropertiesFormBuilderModifier to every extra-property form field
 * (wrapped in Assert\All for localized fields), so the BO form layer surfaces the SAME
 * refusals as the other write paths, inline on the field. Never persisted with a
 * definition: it is built at form-render time from the definition's type/enum.
 */
final class ExtraPropertyTypeCompatibility extends Constraint
{
    /**
     * Single source of the message wording, also used by
     * ExtraPropertyValidator::validateTypeCompatibility().
     */
    public const MESSAGE_TEMPLATE = 'The value is not compatible with the declared "{{ type }}" field type.';

    public string $message = self::MESSAGE_TEMPLATE;

    /**
     * @param list<string>|null $enumValues CHOICE allowed values (null = unrestricted)
     */
    public function __construct(
        public readonly ExtraPropertyType $type,
        public readonly ?array $enumValues = null,
        ?array $groups = null,
        mixed $payload = null,
    ) {
        parent::__construct([], $groups, $payload);
    }
}
