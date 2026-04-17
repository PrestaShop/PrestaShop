<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace Tests\Unit\Core\Domain\Meta;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Domain\Meta\Command\AddMetaCommand;
use PrestaShop\PrestaShop\Core\Domain\Meta\Exception\MetaConstraintException;

/**
 * Class AddMetaCommandTest
 */
class AddMetaCommandTest extends TestCase
{
    /**
     * @dataProvider getIncorrectPageNames
     */
    public function testItThrowsAnExceptionOnIncorrectOrMissingPageName($incorrectPageName)
    {
        $this->expectException(MetaConstraintException::class);
        $this->expectExceptionCode(MetaConstraintException::INVALID_PAGE_NAME);

        $command = new AddMetaCommand($incorrectPageName);
    }

    /**
     * @dataProvider getIncorrectMultiLanguageNames
     */
    public function testItThrowsAnExceptionOnIncorrectPageTitle($incorrectNames)
    {
        $this->expectException(MetaConstraintException::class);
        $this->expectExceptionCode(MetaConstraintException::INVALID_PAGE_TITLE);

        $command = new AddMetaCommand('correct-page-name');

        $command->setLocalisedPageTitle($incorrectNames);
    }

    /**
     * @dataProvider getIncorrectMultiLanguageNames
     */
    public function testItThrowsAnExceptionOnIncorrectPageDescription($incorrectNames)
    {
        $this->expectException(MetaConstraintException::class);
        $this->expectExceptionCode(MetaConstraintException::INVALID_META_DESCRIPTION);

        $command = new AddMetaCommand('correct-page-name');

        $command->setLocalisedMetaDescription($incorrectNames);
    }

    public function getIncorrectPageNames()
    {
        return [
            [
                null,
            ],
            [
                '',
            ],
            [
                'wrong-page-name{}',
            ],
        ];
    }

    public function getIncorrectMultiLanguageNames()
    {
        return [
            [
                [
                    '#$%^@{}',
                ],
            ],
        ];
    }
}
