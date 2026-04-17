<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
