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

namespace PrestaShopBundle\Controller\Admin\Common;

use PrestaShop\PrestaShop\Core\Form\Entity\FormFactory\EntityFormFactoryInterface;
use PrestaShop\PrestaShop\Core\Form\Entity\ResponseHandler\EntryResponseHandlerInterface;
use PrestaShop\PrestaShop\Core\Form\Entity\ResponseHandler\FailureResponseHandlerInterface;
use PrestaShop\PrestaShop\Core\Form\Entity\ResponseHandler\SuccessResponseHandlerInterface;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class EntityController handles most common entity actions
 */
class EntityController extends FrameworkBundleAdminController
{
    /**
     * Add new entity.
     *
     * @param Request $request
     * @param EntryResponseHandlerInterface $entryResponseHandler
     * @param SuccessResponseHandlerInterface $successResponseHandler
     * @param FailureResponseHandlerInterface $failureResponseHandler
     * @param EntityFormFactoryInterface $entityFormFactory
     *
     * @return Response
     */
    public function addAction(
        Request $request,
        EntryResponseHandlerInterface $entryResponseHandler,
        SuccessResponseHandlerInterface $successResponseHandler,
        FailureResponseHandlerInterface $failureResponseHandler,
        EntityFormFactoryInterface $entityFormFactory
    ) {
        $entityForm = $entityFormFactory->getForm();
        $entityForm->handleRequest($request);

        if ($entityForm->isSubmitted()) {
            $errors = []; // @todo: form data saving should be performed here

            if (empty($errors)) {
                $successResponse = $successResponseHandler->getSuccessResponse($request);

                if ($successResponse instanceof Response) {
                    return $successResponse;
                }
            } else {
                $failureResponse = $failureResponseHandler->getFailureResponse($request);

                if ($failureResponse instanceof Response) {
                    return $failureResponse;
                }
            }
        }

        return $entryResponseHandler->getEntryResponse($request, $entityForm);
    }
}
