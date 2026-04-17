<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\ApiPlatform\Metadata;

use Attribute;
use Symfony\Component\Serializer\Attribute\Context;

/**
 * This attribute can be added on a property in an API resource class, when set the localized values
 * are no longer index by Language IDs but by Language's locale instead. It impacts both inputs and outputs
 * where JSON localized value must be indexed by locale:
 *
 *   {names: {"2": "english name", "4": "nom français"}} => {"names": {"en-US": "english name", "fr-FR": "nom français"}}
 */
#[Attribute(Attribute::TARGET_PROPERTY | Attribute::TARGET_CLASS | Attribute::TARGET_METHOD | Attribute::IS_REPEATABLE)]
class LocalizedValue extends Context
{
    public const IS_LOCALIZED_VALUE = 'is_localized_value';

    public const LOCALIZED_VALUE_PARAMETERS = 'localized_value_parameters';

    public const DENORMALIZED_KEY = 'denormalized_key';

    public const NORMALIZED_KEY = 'normalized_key';

    public const LOCALE_KEY = 'locale_key';

    public const ID_KEY = 'id_key';

    public function __construct(string $denormalizedKey = self::LOCALE_KEY, string $normalizedKey = self::LOCALE_KEY, array $localizedParameters = [], array $context = [], array $normalizationContext = [], array $denormalizationContext = [], array|string $groups = [])
    {
        $localeValueContext = [
            // Indicate if denormalized value should use locale or ID
            self::DENORMALIZED_KEY => $denormalizedKey,
            // Indicate if normalized value should use locale or ID
            self::NORMALIZED_KEY => $normalizedKey,
            // Default behaviour is to add the attribute on a property so this context option is true
            self::IS_LOCALIZED_VALUE => empty($localizedParameters),
            // You can set the attribute on a class and define a list of localized parameters
            // (also used to dynamically change the context without using this Attribute)
            // The array is an array where key is the attribute name and value is either true or an array with extra context
            self::LOCALIZED_VALUE_PARAMETERS => $localizedParameters,
        ];

        parent::__construct($localeValueContext + $context, $normalizationContext, $denormalizationContext, $groups);
    }
}
