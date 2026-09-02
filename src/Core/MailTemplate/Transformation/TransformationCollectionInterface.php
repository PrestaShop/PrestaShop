<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\MailTemplate\Transformation;

use IteratorAggregate;

/**
 * TransformationCollectionInterface contains a list of transformations applied
 * on mail templates when they are generated.
 */
interface TransformationCollectionInterface extends IteratorAggregate, \Countable
{
    /**
     * @param TransformationInterface $transformation
     *
     * @return bool
     */
    public function contains($transformation);

    /**
     * @param TransformationInterface $transformation
     */
    public function add($transformation);

    /**
     * @param TransformationInterface $transformation
     */
    public function remove($transformation);
}
