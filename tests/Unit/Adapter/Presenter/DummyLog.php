<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace Tests\Unit\Adapter\Presenter;

class DummyLog
{
    private $pingCounter = 0;

    public function ping()
    {
        ++$this->pingCounter;
    }

    /**
     * @return int
     */
    public function getPingCounter(): int
    {
        return $this->pingCounter;
    }
}
