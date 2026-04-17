<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Behaviour\Features\Context\Domain;

use Behat\Gherkin\Node\TableNode;
use PHPUnit\Framework\Assert;
use PrestaShop\PrestaShop\Core\Domain\Profile\Command\AddProfileCommand;
use PrestaShop\PrestaShop\Core\Domain\Profile\Command\DeleteProfileCommand;
use PrestaShop\PrestaShop\Core\Domain\Profile\Command\EditProfileCommand;
use PrestaShop\PrestaShop\Core\Domain\Profile\Exception\ProfileException;
use PrestaShop\PrestaShop\Core\Domain\Profile\Exception\ProfileNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Profile\Query\GetProfileForEditing;
use PrestaShop\PrestaShop\Core\Domain\Profile\QueryResult\EditableProfile;
use Profile;

class ProfileFeatureContext extends AbstractDomainFeatureContext
{
    /**
     * @Given profile :productReference with default name :profileName exists
     *
     * @param string $profileReference
     * @param string $profileName
     */
    public function assertProfileExists(string $profileReference, string $profileName): void
    {
        $profiles = Profile::getProfiles($this->getDefaultLangId());
        $foundProfile = null;
        foreach ($profiles as $profile) {
            if ($profileName === $profile['name']) {
                $foundProfile = $profile;
                break;
            }
        }

        Assert::assertNotNull($foundProfile);
        $this->getSharedStorage()->set($profileReference, (int) $foundProfile['id_profile']);
    }

    /**
     * @When I add a profile :profileReference with following information:
     *
     * @param string $profileReference
     * @param TableNode $table
     */
    public function addProfile(string $profileReference, TableNode $table): void
    {
        $data = $this->localizeByRows($table);
        $profileId = $this->getCommandBus()->handle(new AddProfileCommand($data['name']));
        $this->getSharedStorage()->set($profileReference, $profileId->getValue());
    }

    /**
     * @When I edit a profile :profileReference with following information:
     *
     * @param string $profileReference
     * @param TableNode $table
     */
    public function editProfile(string $profileReference, TableNode $table): void
    {
        $data = $this->localizeByRows($table);
        $this->getCommandBus()->handle(new EditProfileCommand(
            $this->getSharedStorage()->get($profileReference),
            $data['name']
        ));
    }

    /**
     * @When I delete profile :profileReference
     *
     * @param string $profileReference
     */
    public function deleteProfile(string $profileReference): void
    {
        $this->getCommandBus()->handle(new DeleteProfileCommand(
            $this->getSharedStorage()->get($profileReference)
        ));
    }

    /**
     * @Then profile :profileReference should have the following information:
     *
     * @param string $profileReference
     * @param TableNode $table
     */
    public function assertProfile(string $profileReference, TableNode $table): void
    {
        $profileId = $this->getSharedStorage()->get($profileReference);
        /** @var EditableProfile $editableProfile */
        $editableProfile = $this->getQueryBus()->handle(new GetProfileForEditing($profileId));

        $data = $this->localizeByRows($table);
        if (isset($data['names'])) {
            Assert::assertEquals($editableProfile->getLocalizedNames(), $data['names']);
        }
        if (isset($data['avatarUrl'])) {
            Assert::assertEquals($editableProfile->getAvatarUrl(), $data['avatarUrl']);
        }
    }

    /**
     * @Then profile :profileReference cannot be found
     *
     * @param string $profileReference
     */
    public function assertProfileNotFound(string $profileReference): void
    {
        $caughtException = null;
        try {
            $this->getQueryBus()->handle(new GetProfileForEditing($this->getSharedStorage()->get($profileReference)));
        } catch (ProfileException $e) {
            $caughtException = $e;
        }

        Assert::assertInstanceOf(ProfileNotFoundException::class, $caughtException);
    }
}
