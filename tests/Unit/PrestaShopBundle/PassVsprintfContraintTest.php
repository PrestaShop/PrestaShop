<?php
/**
 * 2007-2017 PrestaShop
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
 * @copyright 2007-2017 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace Tests\Unit\PrestaShopBundle;

use PrestaShopBundle\Entity\Translation;
use PrestaShopBundle\Translation\Constraints\PassVsprintf;
use PrestaShopBundle\Translation\Constraints\PassVsprintfValidator;
use Symfony\Component\Validator\Tests\Constraints\AbstractConstraintValidatorTest;
use Symfony\Component\Validator\Validation;

class PassVsprintfContraintTest extends AbstractConstraintValidatorTest
{
    protected function getApiVersion()
    {
        return Validation::API_VERSION_2_5;
    }

    protected function createValidator()
    {
        return new PassVsprintfValidator();
    }

    public function testEmptyTranslationIsValid()
    {
        $this->validator->validate(new Translation(), new PassVsprintf());

        $this->assertNoViolation();
    }

    public function testTranslationIsValid()
    {
        $translation = (new Translation())
            ->setKey('List of products by brand %s')
            ->setTranslation('Liste des produits de la marque %s');
        $this->validator->validate($translation, new PassVsprintf());

        $this->assertNoViolation();
    }

    public function testNotValid()
    {
        $translation = (new Translation())
            ->setKey('List of products by brand %s')
            ->setTranslation('Liste des produits de la marque nope');
        $constraint = new PassVsprintf();

        $this->validator->validate($translation, $constraint);

        $this->buildViolation($constraint->message)->assertRaised();
    }
}
