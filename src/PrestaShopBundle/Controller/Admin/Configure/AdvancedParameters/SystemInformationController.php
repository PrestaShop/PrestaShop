<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Controller\Admin\Configure\AdvancedParameters;

use PrestaShop\PrestaShop\Adapter\Requirement\CheckMissingOrUpdatedFiles;
use PrestaShop\PrestaShop\Adapter\Requirement\CheckRequirements;
use PrestaShop\PrestaShop\Adapter\System\SystemInformation;
use PrestaShopBundle\Controller\Admin\PrestaShopAdminController;
use PrestaShopBundle\Security\Attribute\AdminSecurity;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Responsible of "Configure > Advanced Parameters > Information" page display.
 */
class SystemInformationController extends PrestaShopAdminController
{
    /**
     * @param Request $request
     *
     * @return Response
     */
    #[AdminSecurity("is_granted('read', request.get('_legacy_controller'))", message: 'Access denied.')]
    public function indexAction(
        Request $request,
        CheckRequirements $checkRequirements,
        SystemInformation $systemInformation,
    ): Response {
        $legacyController = $request->get('_legacy_controller');
        $requirementsSummary = $checkRequirements->getSummary();
        $systemInformationSummary = $systemInformation->getSummary();

        return $this->render('@PrestaShop/Admin/Configure/AdvancedParameters/system_information.html.twig', [
            'layoutHeaderToolbarBtn' => [],
            'layoutTitle' => $this->trans('Information', [], 'Admin.Navigation.Menu'),
            'requireBulkActions' => false,
            'showContentHeader' => true,
            'enableSidebar' => true,
            'help_link' => $this->generateSidebarLink($legacyController),
            'requireFilterStatus' => false,
            'errorMessage' => 'ok',
            'system' => $systemInformationSummary,
            'requirements' => $requirementsSummary,
            'userAgent' => $request->headers->get('User-Agent'),
        ]);
    }

    /**
     * @return JsonResponse
     */
    #[AdminSecurity("is_granted('read', request.get('_legacy_controller'))", message: 'Access denied.')]
    public function displayCheckFilesAction(
        CheckMissingOrUpdatedFiles $requiredFilesChecker,
    ): JsonResponse {
        return new JsonResponse($requiredFilesChecker->getListOfUpdatedFiles());
    }
}
