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
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
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
