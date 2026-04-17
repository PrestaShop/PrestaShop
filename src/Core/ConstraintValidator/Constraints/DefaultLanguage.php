<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints;

use Attribute;
use PrestaShop\PrestaShop\Core\ConstraintValidator\DefaultLanguageValidator;
use Symfony\Component\Validator\Constraint;

/**
 * Class DefaultLanguage is responsible for checking if the array contains default language id - its common to require
 * default language to be presented when saving required multi-language fields.
 */
#[Attribute(Attribute::TARGET_PROPERTY | Attribute::TARGET_METHOD | Attribute::IS_REPEATABLE)]
class DefaultLanguage extends Constraint
{
    public $message = 'The field %field_name% is required at least in your default language.';

    public string $fieldName = '';

    /**
     * If null value is allowed the array value can be null and the constraint won't be applied
     * If the value is an array but no index matching the default language is present it also won't be applied
     * The constraint will only apply for explicit value on the default language that is empty
     */
    public bool $allowNull = false;

    public function __construct(mixed $options = null, ?array $groups = null, mixed $payload = null, ?string $fieldName = null, ?bool $allowNull = null)
    {
        parent::__construct($options, $groups, $payload);
        $this->fieldName = $fieldName ?? '';
        $this->allowNull = $allowNull ?? $this->allowNull;
    }

    /**
     * {@inheritdoc}
     */
    public function validatedBy()
    {
        return DefaultLanguageValidator::class;
    }
}
