<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Import;

/**
 * Interface StringNormalizerInterface describes a string normalizer.
 */
interface StringNormalizerInterface
{
    /**
     * Normalizes a string value.
     *
     * @param string $value
     *
     * @return string normalized string
     */
    public function normalize($value);
}
