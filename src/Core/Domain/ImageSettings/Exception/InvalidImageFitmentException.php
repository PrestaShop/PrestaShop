<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\ImageSettings\Exception;

/**
 * Thrown when an image fitment value is not supported by the ImageSettings domain.
 */
class InvalidImageFitmentException extends ImageTypeException
{
    /**
     * @param string $imageFitment Invalid image fitment value
     */
    public function __construct(string $imageFitment)
    {
        parent::__construct(sprintf('Invalid image fitment "%s".', $imageFitment));
    }
}
