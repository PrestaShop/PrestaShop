<?php
/**
 * Created by PhpStorm.
 * User: tomas
 * Date: 19.2.19
 * Time: 16.32
 */

namespace PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints;


use PrestaShop\PrestaShop\Core\ConstraintValidator\IsCleanHtmlValidator;
use Symfony\Component\Validator\Constraint;

class IsCleanHtml extends Constraint
{
    public $message = '%s is invalid.';

    /**
     * {@inheritdoc}
     */
    public function validatedBy()
    {
        return IsCleanHtmlValidator::class;
    }
}
