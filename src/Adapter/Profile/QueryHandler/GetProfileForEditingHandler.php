<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace PrestaShop\PrestaShop\Adapter\Profile\QueryHandler;

use PrestaShop\PrestaShop\Adapter\Domain\AbstractObjectModelHandler;
use PrestaShop\PrestaShop\Core\Domain\Profile\Exception\ProfileNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Profile\Query\GetProfileForEditing;
use PrestaShop\PrestaShop\Core\Domain\Profile\QueryHandler\GetProfileForEditingHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Profile\QueryResult\EditableProfile;
use PrestaShop\PrestaShop\Core\Domain\Profile\ValueObject\ProfileId;
use PrestaShop\PrestaShop\Core\Image\Parser\ImageTagSourceParser;
use PrestaShop\PrestaShop\Core\Image\Parser\ImageTagSourceParserInterface;
use Profile;

/**
 * Gets Profile for editing using legacy object model
 */
final class GetProfileForEditingHandler extends AbstractObjectModelHandler implements GetProfileForEditingHandlerInterface
{
    /**
     * @var ImageTagSourceParserInterface
     */
    private $imageTagSourceParser;
    /**
     * @var string
     */
    private $imgDir;

    /**
     * @param ImageTagSourceParserInterface|null $imageTagSourceParser
     * @param string $imgDir
     */
    public function __construct(
        ImageTagSourceParserInterface $imageTagSourceParser = null,
        string $imgDir = _PS_PROFILE_IMG_DIR_
    ) {
        $this->imgDir = $imgDir;
        if (null === $imageTagSourceParser) {
            @trigger_error('The $imageTagSourceParser parameter should not be null, inject your main ImageTagSourceParserInterface service', E_USER_DEPRECATED);
        }
        $this->imageTagSourceParser = $imageTagSourceParser ?? new ImageTagSourceParser(__PS_BASE_URI__);
    }

    /**
     * {@inheritdoc}
     */
    public function handle(GetProfileForEditing $query)
    {
        $profileId = $query->getProfileId();
        $profile = $this->getProfile($profileId);

        $avatarUrl = $this->getAvatarUrl($profileId->getValue());

        return new EditableProfile(
            $profileId,
            $profile->name,
            $avatarUrl ? $avatarUrl['path'] : null
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
