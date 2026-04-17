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

        return $request->request->all() + $request->files->all();
    }

    public function supportsDecoding(string $format)
    {
        return self::FORMAT === $format;
    }
}
