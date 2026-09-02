<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;

/**
 * Class StringArrayToIntegerArrayTransformer is responsible for  applying reverse transformation when form is being
 * submitted. If its array, it casts all elements to integer.
 */
final class StringArrayToIntegerArrayDataTransformer implements DataTransformerInterface
{
    /**
     * {@inheritdoc}
     */
    public function transform($value)
    {
        // No transformation is required here due to this data is being sent to template
        return $value;
    }

    /**
     * {@inheritdoc}
     */
    public function reverseTransform($value)
    {
        if (!is_array($value)) {
            return $value;
        }

        return array_map(function ($item) {
            return (int) $item;
        }, $value);
    }
}
