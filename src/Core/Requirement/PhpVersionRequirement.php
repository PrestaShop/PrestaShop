<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Requirement;

/**
 * The range of PHP versions this release runs on.
 *
 * The installer cannot use this class: it checks the PHP version before Composer's autoloader is
 * reachable, so it carries the same numbers in install-dev/install_version.php. PhpVersionRequirementTest
 * fails if the two ever disagree.
 */
final class PhpVersionRequirement
{
    public const MINIMUM_VERSION_ID = 80100;
    public const MAXIMUM_VERSION_ID = 80599;

    public const MINIMUM_VERSION = '8.1';
    public const MAXIMUM_VERSION = '8.5';

    // This class should not be instantiated
    private function __construct()
    {
    }

    public static function isVersionSupported(int $versionId = PHP_VERSION_ID): bool
    {
        return $versionId >= self::MINIMUM_VERSION_ID && $versionId <= self::MAXIMUM_VERSION_ID;
    }
}
