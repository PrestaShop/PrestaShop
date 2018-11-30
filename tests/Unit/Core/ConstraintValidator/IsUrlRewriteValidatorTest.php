<?php
/**
 * 2007-2018 PrestaShop.
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

namespace Tests\Unit\Core\ConstraintValidator;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\IsUrlRewrite;
use PrestaShop\PrestaShop\Core\ConstraintValidator\IsUrlRewriteValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Class IsUrlRewriteValidatorTest
 */
class IsUrlRewriteValidatorTest extends TestCase
{
    public function testItThrowsUnexpectedTypeExceptionOnIncorrectConstraintProvided()
    {
        $this->expectException(UnexpectedTypeException::class);

        $isUrlRewrite = $this->createUrlRewriteValidator();

        $isUrlRewrite->validate('valid-value', new NotBlank());
    }

    /**
     * @dataProvider incorrectTypeRewriteUrls
     */
    public function testItThrowsUnexpectedTypeExceptionOnIncorrectValueTypeProvided($incorrectTypeRewriteUrl)
    {
        $this->expectException(UnexpectedTypeException::class);

        $isUrlRewrite = $this->createUrlRewriteValidator();

        $isUrlRewrite->validate($incorrectTypeRewriteUrl, new IsUrlRewrite());
    }

    public function incorrectTypeRewriteUrls()
    {
        return [
            [
                [],
            ],
            [
                true,
            ],
        ];
    }

    private function createUrlRewriteValidatorWithAscendingCharsAllowed()
    {
        return new IsUrlRewriteValidator(true);
    }

    private function createUrlRewriteValidator()
    {
        return new IsUrlRewriteValidator(false);
    }
}
