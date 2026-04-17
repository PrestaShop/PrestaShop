<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Presenter;

interface PresenterInterface
{
    /**
     * @param mixed $object
     *
     * @return array|AbstractLazyArray
     */
    public function present($object);
}
