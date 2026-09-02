<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Profile\CommandHandler;

use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\Profile\Command\AddProfileCommand;
use PrestaShop\PrestaShop\Core\Domain\Profile\CommandHandler\AddProfileHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Profile\Exception\ProfileException;
use PrestaShop\PrestaShop\Core\Domain\Profile\ValueObject\ProfileId;
use Profile;

/**
 * Adds new employee profile using legacy object model
 */
#[AsCommandHandler]
final class AddProfileHandler implements AddProfileHandlerInterface
{
    /**
     * {@inheritdoc}
     */
    public function handle(AddProfileCommand $command)
    {
        $profile = new Profile();
        $profile->name = $command->getLocalizedNames();

        if (false === $profile->validateFieldsLang(false)) {
            throw new ProfileException('Cannot create Profile because it contains invalid data');
        }

        if (false === $profile->add()) {
            throw new ProfileException('Failed to create Profile');
        }

        return new ProfileId((int) $profile->id);
    }
}
