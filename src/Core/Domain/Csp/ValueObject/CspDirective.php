<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Csp\ValueObject;

use PrestaShop\PrestaShop\Core\Domain\Csp\Exception\CspConstraintException;

/** The CSP directives the feature curates and reports on; anything else a browser reports is ignored. */
enum CspDirective: string
{
    case BASE_URI = 'base-uri';
    case CHILD_SRC = 'child-src';
    case CONNECT_SRC = 'connect-src';
    case DEFAULT_SRC = 'default-src';
    case FONT_SRC = 'font-src';
    case FORM_ACTION = 'form-action';
    case FRAME_ANCESTORS = 'frame-ancestors';
    case FRAME_SRC = 'frame-src';
    case IMG_SRC = 'img-src';
    case MANIFEST_SRC = 'manifest-src';
    case MEDIA_SRC = 'media-src';
    case OBJECT_SRC = 'object-src';
    case SCRIPT_SRC = 'script-src';
    case SCRIPT_SRC_ATTR = 'script-src-attr';
    case SCRIPT_SRC_ELEM = 'script-src-elem';
    case STYLE_SRC = 'style-src';
    case STYLE_SRC_ATTR = 'style-src-attr';
    case STYLE_SRC_ELEM = 'style-src-elem';
    case WORKER_SRC = 'worker-src';

    /**
     * @throws CspConstraintException when the string is not a supported directive
     */
    public static function fromString(string $directive): self
    {
        return self::tryFrom(strtolower(trim($directive)))
            ?? throw new CspConstraintException(sprintf('Invalid CSP directive "%s".', $directive), CspConstraintException::INVALID_DIRECTIVE);
    }

    /** Collapse granular script-/style- variants onto their parent, which the feature curates and enforces at. */
    public function coarsen(): self
    {
        return match ($this) {
            self::SCRIPT_SRC_ELEM, self::SCRIPT_SRC_ATTR => self::SCRIPT_SRC,
            self::STYLE_SRC_ELEM, self::STYLE_SRC_ATTR => self::STYLE_SRC,
            default => $this,
        };
    }
}
