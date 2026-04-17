<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Tests\Integration\Behaviour\Features\Context\Domain\Product;

use Carrier;

/**
 * This abstract class was introduced during UpdateProductCommand unification process,
 * and which idea is to remove multiple sub-commands and use single UpdateProductCommand instead.
 * This abstract context allows sharing assertions which and some other common methods for both implementations during the transition.
 *
 * @see UpdateProductCommand
 * @see UpdateProductHandlerInterface
 *
 * @todo: need to check if this abstract class is still needed when UpdateProductCommand is fully finished,
 *        because one of the contexts that uses it will be deleted, therefore leaving this abstract class useless.
 */
abstract class AbstractShippingFeatureContext extends AbstractProductFeatureContext
{
    /**
     * @param string[] $carrierReferences
     *
     * @return int[]
     */
    protected function getCarrierReferenceIds(array $carrierReferences): array
    {
        $referenceIds = [];
        foreach ($carrierReferences as $carrierReference) {
            $carrier = new Carrier($this->getSharedStorage()->get($carrierReference));
            $referenceIds[] = (int) $carrier->id_reference;
        }

        return $referenceIds;
    }
}
