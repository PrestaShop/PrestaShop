<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\PrestaShopBundle\Controller\FormFiller;

use InvalidArgumentException;
use PHPUnit\Framework\Assert;
use Symfony\Component\DomCrawler\Field\FormField;
use Symfony\Component\DomCrawler\Form;

class FormChecker
{
    /**
     * @param Form $form
     * @param array $expectedFormData
     *
     * @return Form
     */
    public function checkForm(Form $form, array $expectedFormData): Form
    {
        foreach ($expectedFormData as $fieldName => $expectedFormDatum) {
            if (!is_array($expectedFormDatum)) {
                /** @var FormField $formField */
                $formField = $form->get($fieldName);
                $this->assertFormValue($expectedFormDatum, $formField->getValue(), $fieldName);
            } else {
                throw new InvalidArgumentException('The check for array values has not been implemented yet, your turn!!');
            }
        }

        return $form;
    }

    /**
     * @param mixed $expectedValue
     * @param mixed $formValue
     * @param string $fieldName
     */
    private function assertFormValue($expectedValue, $formValue, string $fieldName): void
    {
        // We use assertTrue instead of assertEquals because when it fails it raises an error related to Closure
        // serialization which makes it very hard to debug (this is because of processIsolation)
        Assert::assertTrue(
            $expectedValue == $formValue,
            sprintf(
                'Invalid value for field %s, expected %s but got %s instead.',
                $fieldName,
                $expectedValue,
                (string) $formValue
            )
        );
    }
}
