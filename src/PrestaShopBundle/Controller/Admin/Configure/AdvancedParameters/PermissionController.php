<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\Controller\Admin\Configure\AdvancedParameters;

use PrestaShop\PrestaShop\Core\Domain\Profile\Exception\ProfileException;
use PrestaShop\PrestaShop\Core\Domain\Profile\Permission\Command\UpdateModulePermissionsCommand;
use PrestaShop\PrestaShop\Core\Domain\Profile\Permission\Command\UpdateTabPermissionsCommand;
use PrestaShop\PrestaShop\Core\Domain\Profile\Permission\Query\GetPermissionsForConfiguration;
use PrestaShop\PrestaShop\Core\Domain\Profile\Permission\QueryResult\ConfigurablePermissions;
use PrestaShopBundle\Controller\Admin\PrestaShopAdminController;
use PrestaShopBundle\Controller\Attribute\AllShopContext;
use PrestaShopBundle\Security\Attribute\AdminSecurity;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Allows permissions configuration for employee profiles in "Configure > Advanced Parameters > Team > Permissions"
 */
#[AllShopContext]
class PermissionController extends PrestaShopAdminController
{
    /**
     * Show permissions configuration page
     *
     * @return Response
     */
    #[AdminSecurity("is_granted('read', request.get('_legacy_controller'))")]
    public function indexAction(): Response
    {
        /** @var ConfigurablePermissions $configurablePermissions */
        $configurablePermissions = $this->dispatchQuery(
            new GetPermissionsForConfiguration(
                $this->getEmployeeContext()->getEmployee()->getProfileId()
            )
        );

        return $this->render(
            '@PrestaShop/Admin/Configure/AdvancedParameters/Permission/index.html.twig',
            [
                'help_link' => $this->generateSidebarLink('AdminAccess'),
                'enableSidebar' => true,
                'layoutTitle' => $this->trans('Permissions', [], 'Admin.Navigation.Menu'),
                'configurablePermissions' => $configurablePermissions,
                'multistoreInfoTip' => $this->trans(
                    'Note that this page is available in all shops context only, this is why your context has just switched.',
                    [],
                    'Admin.Notifications.Info'
                ),
                'multistoreIsUsed' => $this->getShopContext()->isMultiShopUsed() && $this->getShopContext()->getShopConstraint()->getShopId() !== null,
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
    #[AdminSecurity("is_granted('create', request.get('_legacy_controller')) && is_granted('update', request.get('_legacy_controller')) && is_granted('delete', request.get('_legacy_controller'))")]
    public function updateTabPermissionsAction(Request $request): JsonResponse
    {
        if ($this->isDemoModeEnabled()) {
            return $this->json(['success' => false]);
        }

        try {
            $this->dispatchCommand(
                new UpdateTabPermissionsCommand(
                    $request->request->getInt('profile_id'),
                    $request->request->getInt('tab_id'),
                    $request->request->get('permission'),
                    $request->request->getBoolean('is_active')
                )
            );

            $response['success'] = true;
            $responseCode = Response::HTTP_OK;
        } catch (ProfileException) {
            $response['success'] = false;
            $responseCode = Response::HTTP_BAD_REQUEST;
        }

        return $this->json($response, $responseCode);
    }

    /**
     * Updates module permissions for profile
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    #[AdminSecurity("is_granted('create', request.get('_legacy_controller')) && is_granted('update', request.get('_legacy_controller')) && is_granted('delete', request.get('_legacy_controller'))")]
    public function updateModulePermissionsAction(Request $request): JsonResponse
    {
        if ($this->isDemoModeEnabled()) {
            return $this->json(['success' => false]);
        }

        try {
            $this->dispatchCommand(
                new UpdateModulePermissionsCommand(
                    $request->request->getInt('profile_id'),
                    $request->request->getInt('id_module'),
                    $request->request->get('permission'),
                    $request->request->getBoolean('is_active')
                )
            );

            $response['success'] = true;
            $responseCode = Response::HTTP_OK;
        } catch (ProfileException) {
            $response['success'] = false;
            $responseCode = Response::HTTP_BAD_REQUEST;
        }

        return $this->json($response, $responseCode);
    }
}
