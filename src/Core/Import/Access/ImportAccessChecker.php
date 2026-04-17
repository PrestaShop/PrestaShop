<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Import\Access;

use PrestaShop\PrestaShop\Core\Employee\ContextEmployeeProviderInterface;

/**
 * Class ImportAccessChecker is responsible for checking import access.
 */
final class ImportAccessChecker implements ImportAccessCheckerInterface
{
    /**
     * @var ContextEmployeeProviderInterface
     */
    private $contextEmployeeProvider;

    /**
     * @param ContextEmployeeProviderInterface $contextEmployeeProvider
     */
    public function __construct(ContextEmployeeProviderInterface $contextEmployeeProvider)
    {
        $this->contextEmployeeProvider = $contextEmployeeProvider;
    }

    /**
     * {@inheritdoc}
     */
    public function canTruncateData()
    {
        return $this->contextEmployeeProvider->isSuperAdmin();
    }
}
