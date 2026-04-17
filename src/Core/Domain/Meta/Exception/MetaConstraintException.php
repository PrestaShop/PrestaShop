<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Meta\Exception;

/**
 * Is thrown when meta constraints are violated
 */
class MetaConstraintException extends MetaException
{
    /**
     * When meta page name is invalid
     */
    public const INVALID_PAGE_NAME = 1;

    /**
     * When meta url_rewrite is invalid
     */
    public const INVALID_URL_REWRITE = 2;

    /**
     * When meta page title is invalid
     */
    public const INVALID_PAGE_TITLE = 3;

    /**
     * When meta description is invalid
     */
    public const INVALID_META_DESCRIPTION = 4;
}
