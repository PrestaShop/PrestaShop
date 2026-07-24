<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\ApiPlatform\Encoder;

use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

class MultipartDecoderTest extends TestCase
{
    public function testSupportsDecoding(): void
    {
        $decoder = new MultipartDecoder(new RequestStack());

        $this->assertTrue($decoder->supportsDecoding(MultipartDecoder::FORMAT));
        $this->assertFalse($decoder->supportsDecoding('json'));
    }

    public function testItReturnsNullWhenThereIsNoCurrentRequest(): void
    {
        $decoder = new MultipartDecoder(new RequestStack());

        $this->assertNull($decoder->decode('', MultipartDecoder::FORMAT));
    }

    /**
     * Swagger UI and the OpenAPI standard serialize object/array properties as a JSON string in a single
     * multipart part, so the decoder must turn those back into arrays for the denormalizer.
     */
    public function testItDecodesJsonStringPartsIntoArrays(): void
    {
        $decoder = $this->createDecoderForRequest([
            'legends' => '{"en-US":"Some legend","fr-FR":"Légende quelconque"}',
        ]);

        $this->assertSame(
            ['legends' => ['en-US' => 'Some legend', 'fr-FR' => 'Légende quelconque']],
            $decoder->decode('', MultipartDecoder::FORMAT)
        );
    }

    /**
     * Scalar parts (booleans, integers, plain strings) must be left untouched so the normalizer keeps coercing
     * them as before (denormalizationContext DISABLE_TYPE_ENFORCEMENT). Decoding them here would change their type.
     *
     * @dataProvider getScalarParts
     */
    public function testItLeavesScalarPartsAsStrings(string $value): void
    {
        $decoder = $this->createDecoderForRequest(['field' => $value]);

        $this->assertSame(['field' => $value], $decoder->decode('', MultipartDecoder::FORMAT));
    }

    public static function getScalarParts(): iterable
    {
        yield 'boolean true' => ['true'];
        yield 'boolean false' => ['false'];
        yield 'integer' => ['0'];
        yield 'plain string' => ['some legend'];
        yield 'null literal' => ['null'];
    }

    /**
     * PHP already parses the bracket syntax (legends[en-US]=value) into a real array, which must be preserved.
     */
    public function testItPreservesArraysProducedByBracketSyntax(): void
    {
        $decoder = $this->createDecoderForRequest([
            'legends' => ['en-US' => 'Some legend', 'fr-FR' => 'Légende quelconque'],
        ]);

        $this->assertSame(
            ['legends' => ['en-US' => 'Some legend', 'fr-FR' => 'Légende quelconque']],
            $decoder->decode('', MultipartDecoder::FORMAT)
        );
    }

    public function testItMergesUploadedFiles(): void
    {
        $file = new UploadedFile(__FILE__, 'image.jpg', null, null, true);
        $decoder = $this->createDecoderForRequest(
            ['legends' => '{"en-US":"Some legend"}'],
            ['image' => $file]
        );

        $decoded = $decoder->decode('', MultipartDecoder::FORMAT);

        $this->assertSame(['en-US' => 'Some legend'], $decoded['legends']);
        $this->assertSame($file, $decoded['image']);
    }

    /**
     * @param array<string, mixed> $requestParameters
     * @param array<string, UploadedFile> $files
     */
    private function createDecoderForRequest(array $requestParameters, array $files = []): MultipartDecoder
    {
        $request = new Request([], $requestParameters, [], [], $files);
        $requestStack = new RequestStack();
        $requestStack->push($request);

        return new MultipartDecoder($requestStack);
    }
}
