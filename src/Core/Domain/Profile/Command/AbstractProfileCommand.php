<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Profile\Command;

use PrestaShop\PrestaShop\Core\Domain\Profile\Exception\ProfileConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Profile\Exception\ProfileException;
use PrestaShop\PrestaShop\Core\Domain\Profile\ProfileSettings;

abstract class AbstractProfileCommand
{
    /**
     * @var string[]
     */
    protected $localizedNames;

    /**
     * @param string[] $localizedNames
     *
     * @throws ProfileConstraintException
     */
    public function __construct(array $localizedNames)
    {
        if (empty($localizedNames)) {
            throw new ProfileException('Profile name cannot be empty');
        }

        foreach ($localizedNames as $localizedName) {
            $this->assertNameIsStringAndRequiredLength($localizedName);
        }
        $this->localizedNames = $localizedNames;
    }

    /**
     * @return string[]
     */
    public function getLocalizedNames()
    {
        return $this->localizedNames;
    }

    /**
     * @param mixed $name
     */
    protected function assertNameIsStringAndRequiredLength($name)
    {
        if (null !== $name && !is_string($name) || strlen($name) > ProfileSettings::NAME_MAX_LENGTH) {
            throw new ProfileConstraintException(
                sprintf(
                    'Profile name should not exceed %d characters length but %s given',
                    ProfileSettings::NAME_MAX_LENGTH,
                    var_export($name, true)
                ),
                ProfileConstraintException::INVALID_NAME
            );
        }
    }
}
