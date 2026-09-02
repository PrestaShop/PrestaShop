<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Profile\QueryHandler;

use PrestaShop\PrestaShop\Adapter\Domain\AbstractObjectModelHandler;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsQueryHandler;
use PrestaShop\PrestaShop\Core\Domain\Profile\Exception\ProfileNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Profile\Query\GetProfileForEditing;
use PrestaShop\PrestaShop\Core\Domain\Profile\QueryHandler\GetProfileForEditingHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Profile\QueryResult\EditableProfile;
use PrestaShop\PrestaShop\Core\Domain\Profile\ValueObject\ProfileId;
use PrestaShop\PrestaShop\Core\Image\Parser\ImageTagSourceParserInterface;
use Profile;

/**
 * Gets Profile for editing using legacy object model
 */
#[AsQueryHandler]
final class GetProfileForEditingHandler extends AbstractObjectModelHandler implements GetProfileForEditingHandlerInterface
{
    /**
     * @var string
     */
    private $defaultAvatarUrl;
    /**
     * @var ImageTagSourceParserInterface
     */
    private $imageTagSourceParser;
    /**
     * @var string
     */
    private $imgDir;

    /**
     * @param ImageTagSourceParserInterface $imageTagSourceParser
     * @param string $imgDir
     * @param string $defaultAvatarUrl
     */
    public function __construct(
        string $defaultAvatarUrl,
        ImageTagSourceParserInterface $imageTagSourceParser,
        string $imgDir = _PS_PROFILE_IMG_DIR_
    ) {
        $this->imgDir = $imgDir;
        $this->imageTagSourceParser = $imageTagSourceParser;
        $this->defaultAvatarUrl = $defaultAvatarUrl;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(GetProfileForEditing $query): EditableProfile
    {
        $profileId = $query->getProfileId();
        $profile = $this->getProfile($profileId);

        $avatarUrl = $this->getAvatarUrl($profileId->getValue());

        return new EditableProfile(
            $profileId,
            $profile->name,
            $avatarUrl ? $avatarUrl['path'] : $this->defaultAvatarUrl
        );
    }

    /**
     * @param ProfileId $profileId
     *
     * @return Profile
     *
     * @throws ProfileNotFoundException
     */
    private function getProfile(ProfileId $profileId)
    {
        $profile = new Profile($profileId->getValue());

        if ($profile->id !== $profileId->getValue()) {
            throw new ProfileNotFoundException(sprintf('Profile with id "%s" was not found', $profileId->getValue()));
        }

        return $profile;
    }

    /**
     * @param int $imageId
     *
     * @return array|null
     */
    private function getAvatarUrl(int $imageId): ?array
    {
        $imagePath = $this->imgDir . $imageId . '.jpg';
        $imageTag = $this->getTmpImageTag($imagePath, $imageId, 'profile');
        $imageSize = $this->getImageSize($imagePath);

        if (empty($imageTag) || null === $imageSize) {
            return null;
        }

        return [
            'size' => sprintf('%skB', $imageSize),
            'path' => $this->imageTagSourceParser->parse($imageTag),
        ];
    }
}
