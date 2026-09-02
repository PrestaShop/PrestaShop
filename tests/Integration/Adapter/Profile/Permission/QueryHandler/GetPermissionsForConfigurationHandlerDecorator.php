<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Adapter\Profile\Permission\QueryHandler;

use PrestaShop\PrestaShop\Adapter\Profile\Permission\QueryHandler\GetPermissionsForConfigurationHandler;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

/**
 * We need this decorator only to mock the AuthorizationCheckerInterface service since no token is available in test
 * environment.
 */
class GetPermissionsForConfigurationHandlerDecorator extends GetPermissionsForConfigurationHandler
{
    public function __construct(
        AuthorizationCheckerInterface $authorizationChecker,
        int $languageId,
        array $nonConfigurableTabs
    ) {
        parent::__construct($authorizationChecker, $languageId, $nonConfigurableTabs);
    }
}
