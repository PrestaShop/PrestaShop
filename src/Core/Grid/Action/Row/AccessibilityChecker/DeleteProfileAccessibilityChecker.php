<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Grid\Action\Row\AccessibilityChecker;

/**
 * Class DeleteProfileAccessibilityChecker.
 *
 * @internal
 */
final class DeleteProfileAccessibilityChecker implements AccessibilityCheckerInterface
{
    /**
     * @var int
     */
    private $superAdminProfileId;

    /**
     * @param int $superAdminProfileId
     */
    public function __construct($superAdminProfileId)
    {
        $this->superAdminProfileId = $superAdminProfileId;
    }

    /**
     * {@inheritdoc}
     */
    public function isGranted(array $profile)
    {
        return $profile['id_profile'] != $this->superAdminProfileId;
    }
}
