<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Profile\QueryResult;

use PrestaShop\PrestaShop\Core\Domain\Profile\ValueObject\ProfileId;

/**
 * Transfers editable Profile data
 */
class EditableProfile
{
    /**
     * @var ProfileId
     */
    private $profileId;

    /**
     * @var string[] As langId => name
     */
    private $localizedNames;

    /**
     * @var string|null
     */
    private $avatarUrl;

    /**
     * @param ProfileId $profileId
     * @param string[] $localizedNames
     * @param string|null $avatarUrl
     */
    public function __construct(
        ProfileId $profileId,
        array $localizedNames,
        ?string $avatarUrl = null
    ) {
        $this->profileId = $profileId;
        $this->localizedNames = $localizedNames;
        $this->avatarUrl = $avatarUrl;
    }

    /**
     * @return ProfileId
     */
    public function getProfileId()
    {
        return $this->profileId;
    }

    /**
     * @return array
     */
    public function getLocalizedNames()
    {
        return $this->localizedNames;
    }

    /**
     * @deprecated Since PrestaShop 8.1, this method only returns string (and not null values)
     *
     * @return string|null
     */
    public function getAvatarUrl(): ?string
    {
        return $this->avatarUrl;
    }
}
