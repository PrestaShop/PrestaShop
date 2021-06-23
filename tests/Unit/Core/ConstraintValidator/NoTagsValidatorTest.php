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

namespace Tests\Unit\Core\ConstraintValidator;

use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\NoTags;
use PrestaShop\PrestaShop\Core\ConstraintValidator\NoTagsValidator;
use Symfony\Component\Validator\Test\ConstraintValidatorTestCase;

class NoTagsValidatorTest extends ConstraintValidatorTestCase
{
    public function testItFailsWhenScriptTagsAreGiven()
    {
        $scriptTag = '<script></script>';

        $this->validator->validate($scriptTag, new NoTags());

        $this->buildViolation((new NoTags())->message)
            ->setParameter('%s', '"' . $scriptTag . '"')
            ->assertRaised()
        ;
    }

    public function testItFailsWhenHTMLTagsGiven()
    {
        $htmlTag = '<div class="btn">Button</div>';

        $this->validator->validate($htmlTag, new NoTags());

        $this->buildViolation((new NoTags())->message)
            ->setParameter('%s', '"' . $htmlTag . '"')
            ->assertRaised()
        ;
    }

    public function testItFailsWhenPHPTagsGiven()
    {
        $phpTag = '<?php $_SERVER = "crash"; ?>';

        $this->validator->validate($phpTag, new NoTags());

        $this->buildViolation((new NoTags())->message)
            ->setParameter('%s', '"' . $phpTag . '"')
            ->assertRaised()
        ;
    }

    protected function createValidator(): NoTagsValidator
    {
        return new NoTagsValidator();
    }
}
