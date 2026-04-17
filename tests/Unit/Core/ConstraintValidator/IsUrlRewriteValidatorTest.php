<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace Tests\Unit\Core\ConstraintValidator;

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
     * @var ConfigurationInterface
     */
    private $configurationMockWithAscendingCharsOn;

    public function setUp(): void
    {
        $this->useAscendedChars = false;

        $this->configurationMockWithAscendingCharsOn = $this
            ->getMockBuilder(ConfigurationInterface::class)
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
