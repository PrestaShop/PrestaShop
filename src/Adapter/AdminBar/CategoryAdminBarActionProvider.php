<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\AdminBar;

use PrestaShop\PrestaShop\Core\Security\AdminEmployeeContext;
use PrestaShopBundle\Translation\TranslatorInterface;

final class CategoryAdminBarActionProvider implements AdminBarActionProviderInterface
{
    private const LEGACY_CONTROLLER = 'ADMINCATEGORIES';

    public function __construct(
        private readonly AdminBarPermissionChecker $permissionChecker,
        private readonly TranslatorInterface $translator,
    ) {
    }

    public function getActions(
        AdminBarPageContext $pageContext,
        AdminEmployeeContext $employeeContext
    ): iterable {
        if ($pageContext->getResourceType() !== 'category' || $pageContext->getResourceId() === null) {
            return [];
        }

        if (!$this->permissionChecker->canUpdate(
            $employeeContext->getProfileId(),
            self::LEGACY_CONTROLLER,
        )) {
            return [];
        }

        return [
            new AdminBarAction(
                'category_edit',
                $this->translator->trans('Edit category'),
                [],
                '/category/' . $pageContext->getResourceId(),
            ),
        ];
    }
}
