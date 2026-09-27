<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\AdminBar;

use PrestaShop\PrestaShop\Core\Security\AdminEmployeeContext;
use PrestaShopBundle\Translation\TranslatorInterface;

final class CmsAdminBarActionProvider implements AdminBarActionProviderInterface
{
    private const LEGACY_CONTROLLER = 'ADMINCMSCONTENT';

    public function __construct(
        private readonly AdminBarPermissionChecker $permissionChecker,
        private readonly TranslatorInterface $translator,
    ) {
    }

    public function getActions(
        AdminBarPageContext $pageContext,
        AdminEmployeeContext $employeeContext
    ): iterable {
        if (!$this->permissionChecker->canUpdate(
            $employeeContext->getProfileId(),
            self::LEGACY_CONTROLLER,
        )) {
            return [];
        }

        $resourceId = $pageContext->getResourceId();
        if ($resourceId === null) {
            return [];
        }

        if ($pageContext->getResourceType() === 'cms_category' && $resourceId === 1) {
            return [];
        }

        return match ($pageContext->getResourceType()) {
            'cms' => [
                new AdminBarAction(
                    'cms_edit',
                    $this->translator->trans('Edit CMS page'),
                    [],
                    '/cms/' . $resourceId,
                ),
            ],
            'cms_category' => [
                new AdminBarAction(
                    'cms_category_edit',
                    $this->translator->trans('Edit CMS category'),
                    [],
                    '/cms-category/' . $resourceId,
                ),
            ],
            default => [],
        };
    }
}
