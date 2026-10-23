<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace Tests\Unit\Core\Domain\Profile\Command;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Domain\Profile\Command\AddProfileCommand;
use PrestaShop\PrestaShop\Core\Domain\Profile\Exception\ProfileConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Profile\ProfileSettings;
use Profile;

class AddProfileCommandTest extends TestCase
{
    /**
     * @dataProvider getValidNames
     */
    public function testItAcceptsNamesWithinTheMaximumLength(string $name): void
    {
        $command = new AddProfileCommand([1 => $name]);

        $this->assertSame([1 => $name], $command->getLocalizedNames());
    }

    public static function getValidNames(): iterable
    {
        yield '128 ASCII characters' => [str_repeat('a', 128)];
        yield '128 accented characters' => [str_repeat('é', 128)];
        yield '32 characters with accents' => ['Équipe du développement logiciel'];
    }

    /**
     * @dataProvider getTooLongNames
     */
    public function testItRejectsNamesAboveTheMaximumLength(string $name): void
    {
        $this->expectException(ProfileConstraintException::class);
        $this->expectExceptionCode(ProfileConstraintException::INVALID_NAME);

        new AddProfileCommand([1 => $name]);
    }

    public static function getTooLongNames(): iterable
    {
        yield '129 ASCII characters' => [str_repeat('a', 129)];
        yield '129 accented characters' => [str_repeat('é', 129)];
    }

    public function testTheMaximumLengthMatchesTheObjectModel(): void
    {
        $this->assertSame(
            Profile::$definition['fields']['name']['size'],
            ProfileSettings::NAME_MAX_LENGTH
        );
    }
}
