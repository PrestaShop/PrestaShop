<?php
/**
 * Created by PhpStorm.
 * User: tomas
 * Date: 19.2.19
 * Time: 16.32
 */

namespace PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints;


use PrestaShop\PrestaShop\Core\ConstraintValidator\CleanHtmlValidator;
use Symfony\Component\Validator\Constraint;

/**
 * Class CleanHtml is responsible for validating the html content to prevent from having javascript events
 * or script tags.
 */
class CleanHtml extends Constraint
{
    public $message = '%s is invalid.';

    /**
     * {@inheritdoc}
     */
    public function validatedBy()
    {
        return CleanHtmlValidator::class;
    }
}
