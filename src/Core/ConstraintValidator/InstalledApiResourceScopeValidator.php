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
