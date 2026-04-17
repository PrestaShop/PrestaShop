<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Controller\Api\Improve\Design;

use PrestaShopBundle\Controller\Api\ApiController;
use PrestaShopBundle\Security\Attribute\AdminSecurity;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class PositionsController extends ApiController
{
    /**
     * Update positions.
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    #[AdminSecurity("is_granted('update', request.get('_legacy_controller'))")]
    public function updateAction(Request $request)
    {
        $moduleId = $request->request->getInt('moduleId');
        $hookId = $request->request->getInt('hookId');
        $way = $request->request->getInt('way');
        $positions = $request->request->all('positions');
        $position = (int) array_search($hookId . '_' . $moduleId, $positions) + 1;

        $module = $this->container->get('prestashop.adapter.legacy.module')->getInstanceById($moduleId);
        if (empty($module)) {
            return $this->jsonResponse(
                [
                    'hasError' => true,
                    'errors' => ['This module cannot be loaded.'],
                ],
                $request
            );
        }

        if (!$module->updatePosition($hookId, $way, $position)) {
            return $this->jsonResponse(
                [
                    'hasError' => true,
                    'errors' => ['Cannot update module position.'],
                ],
                $request
            );
        }

        return $this->jsonResponse([], $request);
    }
}
