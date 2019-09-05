<?php
/**
 * 2007-2019 PrestaShop and Contributors
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Core\ConstraintValidator;

use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\IsUrlRewrite;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * Class IsUrlRewriteValidator is responsible of validating url rewrites according to several patterns
 * which differ when ascending urls are enabled or not.
 */
class IsUrlRewriteValidator extends ConstraintValidator
{
    /**
     * @var bool
     */
    private $isAscendedCharsAllowed;

    /**
     * @param bool $isAscendedCharsAllowed
     */
    public function __construct($isAscendedCharsAllowed)
    {
        $this->isAscendedCharsAllowed = $isAscendedCharsAllowed;
    }

    /**
     * {@inheritdoc}
     */
    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof IsUrlRewrite) {
            throw new UnexpectedTypeException($constraint, IsUrlRewrite::class);
        }

        if (null === $value || '' === $value) {
            return;
        }

        if (!is_string($value)) {
            throw new UnexpectedTypeException($value, 'string');
        }

        if (!$this->isUrlRewriteValid($value)) {
            $this->context->buildViolation($constraint->message)
                ->setTranslationDomain('Admin.Notifications.Error')
                ->setParameter('%s', $this->formatValue($value))
                ->addViolation()
            ;
        }
    }

    /**
     * Validates url rewrite according the patterns which vary based on ascended chars allowed setting.
     *
     * @param string $urlRewrite
     *
     * @return false|int
     */
    private function isUrlRewriteValid($urlRewrite)
    {
        $pattern = '/^[_a-zA-Z0-9\-]+$/';

        if ($this->isAscendedCharsAllowed) {
            $pattern = '/^[_a-zA-Z0-9\pL\pS-]+$/u';
        }

        return preg_match($pattern, $urlRewrite);
    }
}
