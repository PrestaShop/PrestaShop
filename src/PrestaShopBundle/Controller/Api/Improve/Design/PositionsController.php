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

namespace PrestaShopBundle\Controller\Api\Improve\Design;

use PrestaShopBundle\Controller\Api\ApiController;
use PrestaShop\PrestaShop\Adapter\Validate;
use PrestaShop\PrestaShop\Adapter\Module\Module;
use PrestaShopBundle\Entity\Repository\CategoryRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Module as LegacyModule;

class PositionsController extends ApiController
{
    /**
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function updateAction(Request $request)
    {
        $moduleId = $request->request->getInt('moduleId');
        $hookId = $request->request->getInt('hookId');
        $way = $request->request->getInt('way');
        $positions = $request->request->get('positions');
        $position = (int) is_array($positions) ? array_search($hookId.'_'.$moduleId, $positions) + 1 : null;

        $module = LegacyModule::getInstanceById($moduleId);
        if (empty($module->id)) {
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

    /**
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function getHookableListAction(Request $request)
    {
        if ($this->isDemoModeEnabled()) {
            return $this->jsonResponse(
                [
                    'hasError' => true,
                    'errors' => ['Live Edit: This functionality has been disabled.'],
                ],
                $request
            );
        }

        try {
            $result = $this->container->get('prestashop.service.improve.design.positions')
                    ->getHookableList($request);
        } catch (Exception\HookModuleNotFoundException $e) {
            $result = [
                'hasError' => true,
                'errors' => ['Live Edit: no module on this page.'],
            ];
        } catch (Exception\InvalidModuleException $e) {
            $result = [
                'hasError' => true,
                'errors' => ['Live Edit: module is invalid.'],
            ];
        }

        return $this->jsonResponse($result, $request);
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function getHookableModuleListAction(Request $request)
    {
        if ($this->isDemoModeEnabled()) {
            return $this->jsonResponse(
                [
                    'hasError' => true,
                    'errors' => ['Live Edit: This functionality has been disabled.'],
                ],
                $request
            );
        }

        return $this->jsonResponse(
            $this->container->get('prestashop.service.improve.design.positions')->getHookableModuleList($request),
            $request
        );
    }
}
