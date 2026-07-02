<?php

/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints;

use Attribute;
use PrestaShop\PrestaShop\Core\ConstraintValidator\ValidExtraPropertyAssociationsValidator;
use Symfony\Component\Validator\Constraint;

/**
 * Validates a newline-separated textarea of extra property association entries
 * (associated_forms / associated_grids / associated_apis) against the placement-entry grammar.
 *
 * Syntax-only: an unknown formId/gridId/URI template is NOT flagged — manually targeting a form
 * or grid not (yet) detected by the catalog is a supported override.
 */
#[Attribute(Attribute::TARGET_PROPERTY | Attribute::TARGET_METHOD | Attribute::IS_REPEATABLE)]
final class ValidExtraPropertyAssociations extends Constraint
{
    /**
     * Available association types, matching the three placement grammars.
     */
    public const TYPE_FORM = 'form';
    public const TYPE_GRID = 'grid';
    public const TYPE_API = 'api';

    public string $message = 'Line %line%: %error%';

    /**
     * One of the TYPE_* constants: which placement grammar the entries must follow.
     */
    public string $type;

    /**
     * {@inheritdoc}
     */
    public function getRequiredOptions(): array
    {
        return ['type'];
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultOption(): string
    {
        return 'type';
    }

    /**
     * {@inheritdoc}
     */
    public function validatedBy(): string
    {
        return ValidExtraPropertyAssociationsValidator::class;
    }
}
