<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Translation\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 * @Target({"CLASS", "ANNOTATION"})
 */
class PassVsprintf extends Constraint
{
    public $message = 'You must specify as many arguments (%d, %s ...) as the original string.';

    public function getTargets()
    {
        return static::CLASS_CONSTRAINT;
    }
}
