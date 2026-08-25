<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Import\Engine\Exception;

/**
 * Two registered importers declare the same entity type: a silent last-wins
 * would let a module shadow another importer (or a core one) by accident.
 * Deliberate replacement goes through service decoration instead.
 */
class DuplicateEntityTypeException extends ImportEngineException
{
}
