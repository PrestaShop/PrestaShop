<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Core\Foundation\IoC\Fixtures;

class ClassWithDep
{
    private $dummy;

    public function __construct(Dummy $dummy)
    {
        $this->dummy = $dummy;
    }
}
