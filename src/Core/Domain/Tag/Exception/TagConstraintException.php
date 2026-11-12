<?php

/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Tag\Exception;

class TagConstraintException extends TagException
{
    /**
     * When id is not valid
     */
    public const INVALID_ID = 10;

    /**
     * When the name is not valid (e.g. made of special characters only, which the search engine cannot index)
     */
    public const INVALID_NAME = 20;
}
