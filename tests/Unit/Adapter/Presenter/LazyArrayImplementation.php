<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Tests\Unit\Adapter\Presenter;

use PrestaShop\PrestaShop\Adapter\Presenter\AbstractLazyArray;

/**
 * Used for testing abstract class AbstractLazyArray
 */
class LazyArrayImplementation extends AbstractLazyArray
{
    private $propertyOneWasCalled = false;

    /**
     * @arrayAccess
     *
     * @return bool
     */
    public function getPropertyOne()
    {
        $this->propertyOneWasCalled = true;

        return false;
    }

    /**
     * @return bool
     */
    public function wasPropertyOneCalled()
    {
        return $this->propertyOneWasCalled;
    }
}
