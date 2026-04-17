<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Form\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Exception\MissingOptionsException;

class TinyMceMaxLength extends Constraint
{
    public const TOO_LONG_ERROR_CODE = 'TOO_LONG_ERROR_CODE';

    public $max;

    /**
     * @var string|null
     */
    public $message;

    public function __construct($options = null)
    {
        if (null !== $options && !is_array($options)) {
            $options = [
                'max' => $options,
                'message' => null,
            ];
        }

        parent::__construct($options);

        if (null === $this->max) {
            throw new MissingOptionsException(sprintf('Option "max" must be given for constraint %s', self::class), ['max']);
        }
    }

    public function validatedBy(): string
    {
        return static::class . 'Validator';
    }
}
