<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\ImageSettings\ValueObject;

use PrestaShop\PrestaShop\Core\Domain\ImageSettings\Exception\InvalidImageFitmentException;

/**
 * Defines supported image fitment values for the ImageSettings domain.
 */
final class ImageFitment
{
    public const FIT = 'fit';
    public const CROP = 'crop';
    public const BOUND = 'bound';

    public const AVAILABLE_VALUES = [
        self::FIT,
        self::CROP,
        self::BOUND,
    ];

    /**
     * Asserts that the provided image fitment is supported.
     *
     * @throws InvalidImageFitmentException
     */
    public static function assertIsValid(string $imageFitment): void
    {
        // Image fitment is intentionally a closed list at domain level.
        if (!in_array($imageFitment, self::AVAILABLE_VALUES, true)) {
            throw new InvalidImageFitmentException($imageFitment);
        }
    }
}
