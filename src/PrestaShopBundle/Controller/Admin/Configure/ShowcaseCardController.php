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

namespace PrestaShopBundle\Controller\Admin\Configure;

use PrestaShop\PrestaShop\Core\Domain\ShowcaseCard\Command\CloseShowcaseCardCommand;
use PrestaShop\PrestaShop\Core\Domain\ShowcaseCard\Exception\InvalidShowcaseCardNameException;
use PrestaShop\PrestaShop\Core\Domain\ShowcaseCard\ValueObject\ShowcaseCard;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use PrestaShopBundle\Security\Annotation\AdminSecurity;
use PrestaShopBundle\Security\Annotation\DemoRestricted;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @todo Move this to API
 */
class ShowcaseCardController extends FrameworkBundleAdminController
{
    /**
     * Saves the user preference of closing the showcase card.
     *
     * This action should be performed via POST, and expects two parameters:
     * - int $close=1
     * - string $name Name of the showcase card to close
     *
     * @see ShowcaseCard
     *
     * @AdminSecurity(
     *     "is_granted('create', 'CONFIGURE') && is_granted('update', 'CONFIGURE')"
     * )
     * @DemoRestricted(redirectRoute="admin_metas_index")
     *
     * @return JsonResponse
     */
    public function closeShowcaseCardAction(Request $request)
    {
        // check prerequisites
        if (!$request->isMethod('post') || !$request->request->get('close')) {
            return $this->json(
                [
                    'success' => false,
                    'message' => '',
                ],
                Response::HTTP_BAD_REQUEST
            );
        }

        try {
            $employeeId = $this->getContext()->employee->id;
            $closeShowcaseCard = new CloseShowcaseCardCommand($employeeId, $request->request->get('name'));
            $this->getCommandBus()->handle($closeShowcaseCard);

            return $this->json(
                [
                    'success' => true,
                    'message' => '',
                ]
            );
        } catch (\Exception $e) {
            return $this->json(
                [
                    'success' => false,
                    'message' => $e->getMessage(),
                ],
                ($e instanceof InvalidShowcaseCardNameException) ? Response::HTTP_BAD_REQUEST : Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }
}
