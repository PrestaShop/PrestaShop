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
}
