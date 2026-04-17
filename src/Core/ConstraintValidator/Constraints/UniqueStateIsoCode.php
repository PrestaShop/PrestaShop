<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints;

use Attribute;
use PrestaShop\PrestaShop\Core\ConstraintValidator\UniqueStateIsoCodeValidator;
use Symfony\Component\Validator\Constraint;

/**
 * Unique state iso code validator constraint
 */
#[Attribute(Attribute::TARGET_PROPERTY | Attribute::TARGET_METHOD | Attribute::IS_REPEATABLE)]
class UniqueStateIsoCode extends Constraint
{
    /**
     * @var string
     */
    public $message = 'This ISO code already exists. You cannot create two states with the same ISO code within the same country.';

    /**
     * Exclude (or not) a specific State ID for the search of ISO Code
     *
     * @var int|null
     */
    public $excludeStateId = null;

    /**
     * The country to which the state is associated
     *
     * @var int
     */
    public $countryId;

    /**
     * {@inheritdoc}
     */
    public function getRequiredOptions()
    {
        return ['excludeStateId', 'countryId'];
    }

    /**
     * {@inheritdoc}
     */
    public function validatedBy()
    {
        return UniqueStateIsoCodeValidator::class;
    }
}
