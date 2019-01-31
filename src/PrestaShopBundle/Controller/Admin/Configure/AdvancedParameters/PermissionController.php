<?php
/**
 * 2007-2018 PrestaShop
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
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShopBundle\Controller\Admin\Configure\AdvancedParameters;

use PrestaShop\PrestaShop\Core\Domain\Profile\Exception\ProfileException;
use PrestaShop\PrestaShop\Core\Domain\Profile\Permission\Command\UpdateModulePermissionsCommand;
use PrestaShop\PrestaShop\Core\Domain\Profile\Permission\Command\UpdateTabPermissionsCommand;
use PrestaShop\PrestaShop\Core\Domain\Profile\Permission\Query\GetPermissionsForConfiguration;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use PrestaShopBundle\Security\Voter\PageVoter;
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
     * @param Request $request
     *
     * @return Response
     */
    public function indexAction(Request $request)
    {
        $configurablePermissions = $this->getQueryBus()->handle(new GetPermissionsForConfiguration(
            (int) $this->getContext()->employee->id_profile
        ));

        dump($configurablePermissions);

        $permissions = array('view', 'add', 'edit', 'delete');
        $permissionIds = array('view' => 0, 'add' => 1, 'edit' => 2, 'delete' => 3, 'all' => 4);
        $employeeProfileId = (int) $this->getContext()->employee->id_profile;

        return $this->render(
            '@PrestaShop/Admin/Configure/AdvancedParameters/Permission/index.html.twig',
            [
                'hasEditPermission' =>
                    $this->isGranted(PageVoter::UPDATE, $request->attributes->get('_legacy_controller') . '_'),
                'configurablePermissions' => $configurablePermissions,

                'permissions' => $permissions,
                'permissionIds' => $permissionIds,
                'employeeProfileId' => $employeeProfileId
            ]
        );
    }

    /**
     * Update tab permissions for profile
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function updateTabPermissionsAction(Request $request)
    {
        if ($this->isDemoModeEnabled()) {
            return $this->json(['success' => false]);
        }

        try {
            $this->getQueryBus()->handle(new UpdateTabPermissionsCommand(
                $request->request->getInt('profile_id'),
                $request->request->getInt('tab_id'),
                $request->request->get('permission'),
                $request->request->getBoolean('expected_status')
            ));

            $response['success'] = true;
        } catch (ProfileException $e) {
            $response['success'] = false;
        }

        return $this->json($response);
    }

    /**
     * Updates module permissions for profile
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function updateModulePermissions(Request $request)
    {
        if ($this->isDemoModeEnabled()) {
            return $this->json(['success' => false]);
        }

        try {
            $this->getQueryBus()->handle(new UpdateModulePermissionsCommand(
                $request->request->getInt('profile_id'),
                $request->request->getInt('module_id'),
                $request->request->get('permission'),
                $request->request->getBoolean('expected_status')
            ));

            $response['success'] = true;
        } catch (ProfileException $e) {
            $response['success'] = false;
        }

        return $this->json($response);
    }
}
