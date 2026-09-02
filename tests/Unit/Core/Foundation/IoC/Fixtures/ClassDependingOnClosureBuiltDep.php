<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Core\Foundation\IoC\Fixtures;

class ClassDependingOnClosureBuiltDep
{
    private $dep;

    public function __construct(DepBuiltByClosure $dep)
    {
        $this->dep = $dep;
    }

    public function getDep()
    {
        return $this->dep;
    }
}
