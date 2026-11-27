<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Image;

/**
 * Reads an image's intrinsic dimensions, whatever the format.
 *
 * WHY static: the callers that need this are legacy classes without a container - the front controller,
 * the PDF templates, the webservice image handler - and they all repeated the same unchecked
 * `getimagesize()` destructuring. A single entry point they can all reach is worth more here than
 * constructor injection none of them can perform.
 */
final class ImageDimensions
{
    /**
     * WHY: `getimagesize()` only understands raster headers. For an SVG - which the shop logo is
     * allowed to be - it returns `false`, so destructuring its result gave two nulls, and on PHP 8.5
     * a "Cannot use bool as array" diagnostic as well. Callers want a size, so one is always returned.
     *
     * @return array{0: int, 1: int} width and height, both 0 when neither can be determined
     */
    public static function of(string $path): array
    {
        $size = @getimagesize($path);
        if (is_array($size)) {
            return [(int) $size[0], (int) $size[1]];
        }

        return self::ofSvg($path);
    }

    /**
     * Reads the intrinsic size of an SVG, from its width and height attributes when they are absolute,
     * and from its viewBox otherwise. A relative size such as "100%" carries no intrinsic dimension,
     * so the viewBox is what describes those.
     *
     * @return array{0: int, 1: int}
     */
    private static function ofSvg(string $path): array
    {
        if (!is_readable($path)) {
            return [0, 0];
        }

        $previous = libxml_use_internal_errors(true);
        // LIBXML_NONET keeps the parser from resolving anything over the network.
        $svg = simplexml_load_file($path, 'SimpleXMLElement', LIBXML_NONET);
        libxml_clear_errors();
        libxml_use_internal_errors($previous);

        if (false === $svg) {
            return [0, 0];
        }

        $width = self::parseLength((string) ($svg['width'] ?? ''));
        $height = self::parseLength((string) ($svg['height'] ?? ''));
        if ($width > 0 && $height > 0) {
            return [$width, $height];
        }

        $viewBox = preg_split('/[\s,]+/', trim((string) ($svg['viewBox'] ?? '')));
        if (is_array($viewBox) && count($viewBox) === 4) {
            return [(int) round((float) $viewBox[2]), (int) round((float) $viewBox[3])];
        }

        return [0, 0];
    }

    /**
     * @return int 0 when the length is relative or unusable
     */
    private static function parseLength(string $length): int
    {
        if (!preg_match('/^\s*([0-9]*\.?[0-9]+)\s*(px)?\s*$/i', $length, $matches)) {
            return 0;
        }

        return (int) round((float) $matches[1]);
    }
}
