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
