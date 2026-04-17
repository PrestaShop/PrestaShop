<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace Tests\Unit\Core\Domain\Contact\Command;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Domain\Contact\Command\AddContactCommand;
use PrestaShop\PrestaShop\Core\Domain\Contact\Exception\ContactConstraintException;

/**
 * Class AddContactCommandTest
 */
class AddContactCommandTest extends TestCase
{
    /**
     * @dataProvider getIncorrectTitles
     *
     * @param array $incorrectTitle
     */
    public function testItThrowsAnExceptionOnIncorrectTitle(array $incorrectTitle): void
    {
        $this->expectException(ContactConstraintException::class);
        $this->expectExceptionCode(ContactConstraintException::INVALID_TITLE);

        $command = new AddContactCommand($incorrectTitle, false);
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

        $command = new AddContactCommand(
            [
                'test title',
            ],
            false
        );

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
