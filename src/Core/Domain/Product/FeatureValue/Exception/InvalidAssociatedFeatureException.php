<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Product\FeatureValue\Exception;

/**
 * This exception is thrown when you try to associate a FeatureValue to Feature which
 * it doesn't belong to.
 */
class InvalidAssociatedFeatureException extends ProductFeatureValueException
{
}
