<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\ConstraintValidator;

use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\InstalledApiResourceScope;
use PrestaShopBundle\ApiPlatform\Scopes\ApiResourceScopesExtractorInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class InstalledApiResourceScopeValidator extends ConstraintValidator
{
    public function __construct(
        private readonly ApiResourceScopesExtractorInterface $apiResourceScopesExtractor
    ) {
    }

    /**
     * @param string[] $value
     * @param Constraint $constraint
     */
    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof InstalledApiResourceScope) {
            throw new UnexpectedTypeException($constraint, InstalledApiResourceScope::class);
        }

        if (!is_array($value) || (count($value) !== count(array_filter($value, 'is_string')))) {
            throw new UnexpectedTypeException($value, 'string[]');
        }

        $invalidScopes = [];
        foreach ($value as $scopeToValidate) {
            foreach ($this->apiResourceScopesExtractor->getAllApiResourceScopes() as $apiResourceScopes) {
                foreach ($apiResourceScopes->getScopes() as $apiResourceScope) {
                    if ($apiResourceScope === $scopeToValidate) {
                        continue 3;
                    }
                }
            }

            $invalidScopes[] = $scopeToValidate;
        }

        if (!empty($invalidScopes)) {
            $this->context->buildViolation($constraint->message)
                ->setTranslationDomain('Admin.Notifications.Error')
                ->setParameter(
                    '%scope_names%',
                    implode(',', $invalidScopes)
                )
                ->addViolation()
            ;
        }
    }
}
