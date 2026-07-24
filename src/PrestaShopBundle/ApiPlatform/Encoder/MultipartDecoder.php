<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\ApiPlatform\Encoder;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Serializer\Encoder\DecoderInterface;

/**
 * Additional decoder to handle multipart form data requests.
 */
class MultipartDecoder implements DecoderInterface
{
    public const FORMAT = 'multipart';

    public function __construct(
        private RequestStack $requestStack
    ) {
    }

    public function decode(string $data, string $format, array $context = [])
    {
        $request = $this->requestStack->getCurrentRequest();

        if (!$request) {
            return null;
        }

        // Swagger UI (and the OpenAPI standard) serializes object/array properties as a JSON string in a single
        // multipart part (e.g. legends={"en-US":"value"}), whereas PHP only parses the bracket syntax
        // (legends[en-US]=value) into an array. We decode any part that resolves to an array so these nested
        // structures reach the denormalizer as arrays. Scalar parts (e.g. cover=true, position=0) are left as
        // strings and coerced later by the normalizer (denormalizationContext DISABLE_TYPE_ENFORCEMENT).
        $parameters = array_map(static function ($value) {
            if (!is_string($value)) {
                return $value;
            }

            $decoded = json_decode($value, true);

            return is_array($decoded) ? $decoded : $value;
        }, $request->request->all());

        return $parameters + $request->files->all();
    }

    public function supportsDecoding(string $format)
    {
        return self::FORMAT === $format;
    }
}
