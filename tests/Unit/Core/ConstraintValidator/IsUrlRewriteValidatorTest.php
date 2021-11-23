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

use PHPUnit\Framework\MockObject\Builder\InvocationMocker;
use PrestaShop\PrestaShop\Core\ConfigurationInterface;
use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\IsUrlRewrite;
use PrestaShop\PrestaShop\Core\ConstraintValidator\IsUrlRewriteValidator;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Test\ConstraintValidatorTestCase;

/**
 * Class IsUrlRewriteValidatorTest
 */
class IsUrlRewriteValidatorTest extends ConstraintValidatorTestCase
{
    /**
     * @var bool
     */
    private $useAscendedChars;

    /**
     * @var InvocationMocker
     */
    private $configurationMockWithAscendingCharsOn;

    public function setUp(): void
    {
        $this->useAscendedChars = false;

        $this->configurationMockWithAscendingCharsOff = $this->getMockBuilder(ConfigurationInterface::class)
            ->getMock()
        ;

        $this->configurationMockWithAscendingCharsOn = $this->getMockBuilder(ConfigurationInterface::class)
            ->getMock()
        ;

        $this->configurationMockWithAscendingCharsOn
            ->method('get')
            ->with('PS_ALLOW_ACCENTED_CHARS_URL')
            ->willReturn(true)
        ;

        parent::setUp();
    }

    public function testItThrowsUnexpectedTypeExceptionOnIncorrectConstraintProvided()
    {
        $this->expectException(UnexpectedTypeException::class);

        $this->validator->validate('valid-value', new NotBlank());
    }

    /**
     * @dataProvider getIncorrectTypeRewriteUrls
     */
    public function testItThrowsUnexpectedTypeExceptionOnIncorrectValueTypeProvided($incorrectTypeRewriteUrl)
    {
        $this->expectException(UnexpectedTypeException::class);

        $this->validator->validate($incorrectTypeRewriteUrl, new IsUrlRewrite());
    }

    /**
     * @dataProvider getIncorrectRewriteUrls
     */
    public function testItFindsIncorrectUrlRewritePattern($incorrectRewriteUrl)
    {
        $this->validator->validate($incorrectRewriteUrl, new IsUrlRewrite());

        $this->buildViolation((new IsUrlRewrite())->message)
            ->setParameter('%s', '"' . $incorrectRewriteUrl . '"')
            ->assertRaised()
        ;
    }

    /**
     * @dataProvider getCorrectRewriteUrls
     */
    public function testItFindsCorrectUrlRewritePatterns($correctRewriteUrl)
    {
        $this->validator->validate($correctRewriteUrl, new IsUrlRewrite());

        $this->assertNoViolation();
    }

    /**
     * @dataProvider getCorrectRewriteUlrUsingAscendingChars
     */
    public function testItFindsCorrectUrlRewritePatternUsingAscendedChars($correctRewriteUrl)
    {
        $this->useAscendedChars = true;

        $validator = $this->createValidator();
        $validator->initialize($this->context);

        $validator->validate($correctRewriteUrl, new IsUrlRewrite());

        $this->assertNoViolation();
    }

    public function getIncorrectTypeRewriteUrls()
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

    public function getIncorrectRewriteUrls()
    {
        return [
            [
                'test@!',
            ],
            [
                '*test2*',
            ],
            [
                'TęstĄČĘĖ',
            ],
            [
                'tes/t/001',
            ],
        ];
    }

    public function getCorrectRewriteUrls()
    {
        return [
            [
                'my-test',
            ],
            [
                'test',
            ],
            [
                '123-589-test',
            ],
        ];
    }

    public function getCorrectRewriteUlrUsingAscendingChars()
    {
        return [
            [
                'aĮأ',
            ],
            [
                'Šarūnas',
            ],
            [
                '_$',
            ],
        ];
    }

    protected function createValidator()
    {
        $configuration = $this->useAscendedChars ?
             $this->configurationMockWithAscendingCharsOn :
             0
         ;

        return new IsUrlRewriteValidator($configuration);
    }
}
