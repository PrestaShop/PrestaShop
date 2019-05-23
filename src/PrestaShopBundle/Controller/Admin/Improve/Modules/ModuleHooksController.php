<?php
/**
 * 2007-2019 PrestaShop and Contributors
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShopBundle\Controller\Admin\Improve\Modules;

use Exception;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Responsible for handling module hooks data
 */
class ModuleHooksController extends FrameworkBundleAdminController
{
    /**
     * Provides available hooks for module in json response
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function getModuleHooksAction(Request $request)
    {
        try {
            $moduleId = (int) $request->query->get('id_module');
            $hooksProvider = $this->get('prestashop.adapter.form.choice_provider.module_hook_by_id');
            $hooks = $hooksProvider->getChoices([
                'id_module' => $moduleId,
            ]);

            return $this->json([
                'hooks' => $hooks,
            ]);
        } catch (Exception $e) {
            return $this->json([
                    'message' => $this->getErrorMessageForException($e, []),
                ],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }
}
