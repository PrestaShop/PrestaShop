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

declare(strict_types=1);

namespace PrestaShopBundle\Controller\Admin\Configure\AdvancedParameters;

use PrestaShop\PrestaShop\Core\Domain\Profile\Exception\ProfileException;
use PrestaShop\PrestaShop\Core\Domain\Profile\Permission\Command\UpdateModulePermissionsCommand;
use PrestaShop\PrestaShop\Core\Domain\Profile\Permission\Command\UpdateTabPermissionsCommand;
use PrestaShop\PrestaShop\Core\Domain\Profile\Permission\Query\GetPermissionsForConfiguration;
use PrestaShop\PrestaShop\Core\Domain\Profile\Permission\QueryResult\ConfigurablePermissions;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use PrestaShopBundle\Security\Annotation\AdminSecurity;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Allows permissions configuration for employee profiles in "Configure > Advanced Parameters > Team > Permissions"
 */
class PermissionController extends FrameworkBundleAdminController
{
    /**
     * Show permissions configuration page
     *
     * @AdminSecurity("is_granted('read', request.get('_legacy_controller'))")
     *
     * @return Response
     */
    public function indexAction(): Response
    {
        /** @var ConfigurablePermissions $configurablePermissions */
        $configurablePermissions = $this->getQueryBus()->handle(
            new GetPermissionsForConfiguration(
                (int) $this->getContext()->employee->id_profile
            )
        );

        return $this->render(
            '@PrestaShop/Admin/Configure/AdvancedParameters/Permission/index.html.twig',
            [
                'configurablePermissions' => $configurablePermissions,
            ]
        );
    }

    /**
     * Update tab permissions for profile
     *
     * @AdminSecurity("is_granted(['create', 'update', 'delete'], request.get('_legacy_controller'))")
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function updateTabPermissionsAction(Request $request): JsonResponse
    {
        if ($this->isDemoModeEnabled()) {
            return $this->json(['success' => false]);
        }

        try {
            $this->getQueryBus()->handle(
                new UpdateTabPermissionsCommand(
                    $request->request->getInt('profile_id'),
                    $request->request->getInt('tab_id'),
                    $request->request->get('permission'),
                    $request->request->getBoolean('expected_status'),
                    $request->request->getBoolean('from_parent')
                )
            );

            $response['success'] = true;
        } catch (ProfileException $e) {
            $response['success'] = false;
        }

        return $this->json($response);
    }

    /**
     * Updates module permissions for profile
     *
     * @AdminSecurity("is_granted(['create', 'update', 'delete'], request.get('_legacy_controller'))")
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function updateModulePermissionsAction(Request $request): JsonResponse
    {
        if ($this->isDemoModeEnabled()) {
            return $this->json(['success' => false]);
        }

        try {
            $this->getQueryBus()->handle(
                new UpdateModulePermissionsCommand(
                    $request->request->getInt('profile_id'),
                    $request->request->getInt('id_module'),
                    $request->request->get('permission'),
                    $request->request->getBoolean('expected_status')
                )
            );

            $response['success'] = true;
        } catch (ProfileException $e) {
            $response['success'] = false;
        }

        return $this->json($response);
    }
}
