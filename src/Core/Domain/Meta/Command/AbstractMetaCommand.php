<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Meta\Command;

use PrestaShop\PrestaShop\Core\Domain\Meta\Exception\MetaConstraintException;

/**
 * Class AbstractMetaCommand is responsible for defining the abstraction for AddMetaCommand and EditMetaCommand.
 */
abstract class AbstractMetaCommand
{
    /**
     * @param int $languageId
     * @param string $value
     * @param int $constraintErrorCode
     *
     * @throws MetaConstraintException
     */
    protected function assertNameMatchesRegexPattern($languageId, $value, $constraintErrorCode)
    {
        $regex = '/^[^<>{}]*$/u';

        if ($value && !preg_match($regex, $value)) {
            throw new MetaConstraintException(sprintf('Value "%s" for language id %s did not passed the regex expression: %s', $value, $languageId, $regex), $constraintErrorCode);
        }
    }
}
