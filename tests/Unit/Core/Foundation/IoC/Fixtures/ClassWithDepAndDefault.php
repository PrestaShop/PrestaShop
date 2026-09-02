<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Core\Foundation\IoC\Fixtures;

class ClassWithDepAndDefault
{
    private $dummy;
    private $something;

    public function __construct(Dummy $dummy, $something = 4)
    {
        $this->dummy = $dummy;
        $this->something = $something;
    }
}
