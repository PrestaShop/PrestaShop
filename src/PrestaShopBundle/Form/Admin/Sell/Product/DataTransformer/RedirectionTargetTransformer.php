<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\Form\Admin\Sell\Product\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;

/**
 * The form type used for target expects a collection of entities, but the provider only
 * provides one because in this case only one entity is expect (data limit == 1). So this
 * transformer turns the single entity into an array and vice versa.
 */
class RedirectionTargetTransformer implements DataTransformerInterface
{
    /**
     * {@inheritDoc}
     */
    public function transform($redirectionData)
    {
        if (isset($redirectionData['target'])) {
            $redirectionData['target'] = [
                $redirectionData['target'],
            ];
        }

        return $redirectionData;
    }

    /**
     * {@inheritDoc}
     */
    public function reverseTransform($redirectionData)
    {
        // EntitySearchInputType contains a collection of hidden inputs, for redirection only one target is selected
        // and we just want to retrieve the first (and only) selected ID
        if (!empty($redirectionData['target'])) {
            $redirectionData['target'] = reset($redirectionData['target']);
        }

        return $redirectionData;
    }
}
