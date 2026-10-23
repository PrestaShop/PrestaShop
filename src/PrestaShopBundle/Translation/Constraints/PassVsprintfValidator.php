<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

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

        $sourceArgumentCount = $this->countArgumentsOfTranslation($translation->getKey());
        $translatedString = $translation->getTranslation();

        if (0 === $sourceArgumentCount) {
            // Without source format specifiers, the translator never calls vsprintf().
            // Do not mistake percentages followed by prose ("10 % de remise") for arguments.
            $translatedString = preg_replace('/(?<=\d)\h*%\h*(?=\p{L}(?:\p{L}|[\'’]\p{L}))/u', '', $translatedString);
            if (null === $translatedString) {
                throw new Exception('Preg_replace failed');
            }
        }

        if ($sourceArgumentCount != $this->countArgumentsOfTranslation($translatedString)) {
            $this->context->buildViolation($constraint->message)
                ->addViolation();
        }
    }

    private function countArgumentsOfTranslation(string $property): int
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
