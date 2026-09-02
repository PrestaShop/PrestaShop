<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Profile\CommandHandler;

use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\Profile\Command\EditProfileCommand;
use PrestaShop\PrestaShop\Core\Domain\Profile\CommandHandler\EditProfileHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Profile\Exception\ProfileException;
use PrestaShop\PrestaShop\Core\Domain\Profile\Exception\ProfileNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Profile\ValueObject\ProfileId;
use PrestaShopDatabaseException;
use PrestaShopException;
use Profile;

/**
 * Edits Profile using legacy object model
 */
#[AsCommandHandler]
final class EditProfileHandler implements EditProfileHandlerInterface
{
    /**
     * {@inheritdoc}
     */
    public function handle(EditProfileCommand $command)
    {
        $profile = $this->getProfile($command->getProfileId());
        $profile->name = $command->getLocalizedNames();

        if (false === $profile->validateFieldsLang(false)) {
            throw new ProfileException('Cannot edit Profile because it contains invalid data');
        }

        if (false === $profile->update()) {
            throw new ProfileException('Failed to edit Profile');
        }
    }

    /**
     * @param ProfileId $profileId
     *
     * @return Profile
     *
     * @throws ProfileNotFoundException
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    private function getProfile(ProfileId $profileId)
    {
        $profile = new Profile($profileId->getValue());

        if ($profile->id !== $profileId->getValue()) {
            throw new ProfileNotFoundException(sprintf('Profile with id "%s" was not found', $profileId->getValue()));
        }

        return $profile;
    }
}
