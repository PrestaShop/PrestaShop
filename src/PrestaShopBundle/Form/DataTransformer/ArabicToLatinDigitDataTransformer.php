<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\Form\DataTransformer;

use PrestaShop\PrestaShop\Core\Util\ArabicToLatinDigitConverter;
use Symfony\Component\Form\DataTransformerInterface;

/**
 * Class ArabicToLatinDigitDataTransformer is responsible for converting arabic/persian digits to latin digits
 */
final class ArabicToLatinDigitDataTransformer implements DataTransformerInterface
{
    /**
     * @var ArabicToLatinDigitConverter
     */
    private $arabicToLatinDigitConverter;

    public function __construct(ArabicToLatinDigitConverter $arabicToLatinDigitConverter)
    {
        $this->arabicToLatinDigitConverter = $arabicToLatinDigitConverter;
    }

    /**
     * Do not transform latin number to arabic/persian number as
     * the javascript datepicker will handle that on its side
     *
     * {@inheritdoc}
     */
    public function transform($value)
    {
        return $value;
    }

    /**
     * {@inheritdoc}
     */
    public function reverseTransform($value)
    {
        if (null === $value || '' === $value) {
            return null;
        }

        return $this->arabicToLatinDigitConverter->convert($value);
    }
}
