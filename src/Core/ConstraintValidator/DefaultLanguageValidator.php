<?php
/**
 * 2007-2018 PrestaShop.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
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
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Core\ConstraintValidator;

use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\DefaultLanguage;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * Class DefaultLanguageValidator is responsilbe for doing the actual validation under DefaultLanguage constraint.
 */
class DefaultLanguageValidator extends ConstraintValidator
{
    /**
     * @var int
     */
    private $defaultLanguageId;

    /**
     * @var string
     */
    private $defaultLanguageName;

    /**
     * @param int $defaultLanguageId
     * @param string $defaultLanguageName
     */
    public function __construct($defaultLanguageId, $defaultLanguageName)
    {
        $this->defaultLanguageId = $defaultLanguageId;
        $this->defaultLanguageName = $defaultLanguageName;
    }

    /**
     * {@inheritdoc}
     */
    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof DefaultLanguage) {
            throw new UnexpectedTypeException($constraint, DefaultLanguage::class);
        }

        if (!is_array($value)) {
            throw new UnexpectedTypeException($value, 'array');
        }

        if (!isset($value[$this->defaultLanguageId]) || !$value[$this->defaultLanguageId]) {
            $this->context->buildViolation($constraint->message)
                ->setTranslationDomain('Admin.Notifications.Error')
                ->setParameters([
                    '%field_name%' =>
                        null !== $this->context->getObject() ? $this->context->getObject()->getName() : '',
                    '%lang%' => $this->defaultLanguageName,
                ])
                ->addViolation()
            ;
        }
    }
}
