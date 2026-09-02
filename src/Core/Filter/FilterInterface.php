<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Filter;

interface FilterInterface
{
    /**
     * Performs a filter on the subject object.
     *
     * @param mixed $subject subject to filter
     *
     * @return mixed filtered subject
     */
    public function filter($subject);
}
