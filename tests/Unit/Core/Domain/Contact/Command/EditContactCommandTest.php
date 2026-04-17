<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace Tests\Unit\Core\Domain\Contact\Command;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Domain\Contact\Command\EditContactCommand;
use PrestaShop\PrestaShop\Core\Domain\Contact\Exception\ContactConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Contact\Exception\ContactException;

/**
 * Class EditContactCommandTest
 */
class EditContactCommandTest extends TestCase
{
    public function testItThrowsAnExceptionOnIncorrectIdPassed()
    {
        $this->expectException(ContactException::class);
        /** @phpstan-ignore-next-line */
        $command = new EditContactCommand('1');
    }

    /**
     * @dataProvider getIncorrectTitles
     *
     * @param array $incorrectTitle
     */
    public function testItThrowsAnExceptionOnIncorrectTitle(array $incorrectTitle): void
    {
        $this->expectException(ContactConstraintException::class);
        $this->expectExceptionCode(ContactConstraintException::INVALID_TITLE);

        $command = new EditContactCommand(1);
        $command->setLocalisedTitles($incorrectTitle);
    }

    public function getIncorrectTitles(): array
    {
        return [
            [
                [],
            ],
            [
                [
                    '',
                    null,
                    true,
                ],
            ],
            [
                [
                    '{}',
                    'test<=',
                ],
            ],
        ];
    }

    /**
     * @dataProvider getIncorrectShopAssociations
     */
    public function testItThrowsAnExceptionOnIncorrectShopAssociation(array $incorrectShopAssociation): void
    {
        $this->expectException(ContactConstraintException::class);
        $this->expectExceptionCode(ContactConstraintException::INVALID_SHOP_ASSOCIATION);

        $command = new EditContactCommand(1);
        $command->setShopAssociation($incorrectShopAssociation);
    }

    public function getIncorrectShopAssociations(): array
    {
        return [
            [
                [
                    '1',
                ],
            ],
            [
                [
                    null,
                    false,
                ],
            ],
        ];
    }
}
