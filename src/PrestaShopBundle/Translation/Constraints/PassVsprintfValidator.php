<?php
/**
 * 2007-2018 PrestaShop
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

namespace PrestaShopBundle\Translation\Constraints;

use Exception;
use PrestaShopBundle\Entity\Translation;
use PrestaShopBundle\Translation\PrestaShopTranslatorTrait;
use PrestaShopBundle\Translation\Constraints\PassVsprintf;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class PassVsprintfValidator extends ConstraintValidator
{
    public function validate($translation, Constraint $constraint)
    {
        if (!$constraint instanceof PassVsprintf) {
            throw new UnexpectedTypeException($constraint, 'PrestaShopBundle\Translation\Constraints\PassVsprintf');
        }

        if (!$translation instanceof Translation) {
            throw new UnexpectedTypeException($translation, 'PrestaShopBundle\Entity\Translation');
        }

        if ($this->countArgumentsOfTranslation($translation->getKey()) != $this->countArgumentsOfTranslation($translation->getTranslation())) {
            $this->context->buildViolation($constraint->message)
                ->addViolation();
        }
    }

    private function countArgumentsOfTranslation($property)
    {
        $matches = array();
        if (preg_match_all(PrestaShopTranslatorTrait::$regexSprintfParams, $property, $matches) === false) {
            throw new Exception('Preg_match failed');
        }

        return count($matches[0]);
    }
}
