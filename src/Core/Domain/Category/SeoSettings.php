<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Category;

/**
 * Defines settings for Category's SEO
 */
final class SeoSettings
{
    /**
     * Maximum length of SEO title (value is constrained by database)
     */
    public const MAX_TITLE_LENGTH = 255;

    /**
     * Recommended length of SEO title
     */
    public const RECOMMENDED_TITLE_LENGTH = 70;

    /**
     * Maximum length of SEO description (value is constrained by database)
     */
    public const MAX_DESCRIPTION_LENGTH = 512;

    /**
     * Recommended length of SEO description
     */
    public const RECOMMENDED_DESCRIPTION_LENGTH = 160;

    /**
     * Maximum length of link rewrite (value is constrained by database)
     */
    public const MAX_LINK_REWRITE_LENGTH = 128;
}
