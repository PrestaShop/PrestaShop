<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
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
