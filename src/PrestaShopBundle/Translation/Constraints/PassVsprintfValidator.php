<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Translation\Constraints;

use Exception;
use PrestaShopBundle\Entity\Translation;
use PrestaShopBundle\Translation\Translator;
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
        if (empty($property)) {
            return 0;
        }
        $matches = [];
        if (preg_match_all(Translator::$regexSprintfParams, $property, $matches) === false) {
            throw new Exception('Preg_match failed');
        }

        return count($matches[0]);
    }
}
