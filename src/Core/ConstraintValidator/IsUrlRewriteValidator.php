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
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace PrestaShop\PrestaShop\Core\ConstraintValidator;

use PrestaShop\PrestaShop\Core\ConfigurationInterface;
use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\IsUrlRewrite;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

/**
 * Class IsUrlRewriteValidator is responsible of validating url rewrites according to several patterns
 * which differ when ascending urls are enabled or not.
 */
class IsUrlRewriteValidator extends ConstraintValidator
{
    /**
     * @var ConfigurationInterface|bool
     */
    private $accentedCharsConfiguration;

    /**
     * this constructor can accept boolean value of already predefined accented chars allowance configuration to not
     * introduce BC break. The recommended approach is to pass
     * PrestaShop\PrestaShop\Adapter\Configuration as a service instead to avoid keeping cached scalar value.
     *
     * @param ConfigurationInterface|bool $accentedCharsConfiguration
     */
    public function __construct($accentedCharsConfiguration)
    {
        $this->accentedCharsConfiguration = $accentedCharsConfiguration;
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
     * Validates url rewrite according the patterns which vary based on ascented chars allowed setting.
     *
     * @param string $urlRewrite
     *
     * @return false|int
     */
    private function isUrlRewriteValid($urlRewrite)
    {
        $pattern = '/^[_a-zA-Z0-9\-]+$/';

        if ($this->getAllowAccentedCharsSetting()) {
            $pattern = '/^[_a-zA-Z0-9\pL\pS-]+$/u';
        }

        return preg_match($pattern, $urlRewrite);
    }

    /**
     * Gets the accented chars url setting.
     *
     * @return bool
     */
    private function getAllowAccentedCharsSetting()
    {
        if ($this->accentedCharsConfiguration instanceof ConfigurationInterface) {
            return $this->accentedCharsConfiguration->get('PS_ALLOW_ACCENTED_CHARS_URL');
        }

        return $this->accentedCharsConfiguration;
    }
}
