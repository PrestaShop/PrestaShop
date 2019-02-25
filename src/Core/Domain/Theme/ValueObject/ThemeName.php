<?php
/**
 * 2007-2019 PrestaShop and Contributors
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Core\Domain\Theme\ValueObject;

use PrestaShop\PrestaShop\Core\Domain\Theme\Exception\InvalidThemeNameException;

/**
 * Class ThemeName
 */
class ThemeName
{
    /**
     * @var string
     */
    private $themeName;

    /**
     * @param string $themeName
     */
    public function __construct($themeName)
    {
        $this->assertThemeNameIsNotEmptyString($themeName);
        $this->assertThemeNameMatchesPattern($themeName);

        $this->themeName = $themeName;
    }

    /**
     * @return string
     */
    public function getValue()
    {
        return $this->themeName;
    }

    /**
     * @param string $themeName
     *
     * @throws InvalidThemeNameException
     */
    private function assertThemeNameIsNotEmptyString($themeName)
    {
        if (!is_string($themeName) || empty($themeName)) {
            throw new InvalidThemeNameException('Theme name cannot be empty.');
        }
    }

    /**
     * @param string $themeName
     *
     * @throws InvalidThemeNameException
     */
    private function assertThemeNameMatchesPattern($themeName)
    {
        if (!preg_match('/^[a-zA-Z0-9_.-]+$/', $themeName)) {
            throw new InvalidThemeNameException(
                sprintf('Invalid theme name %s provided.', var_export($themeName, true))
            );
        }
    }
}
