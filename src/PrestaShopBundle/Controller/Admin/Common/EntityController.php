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

use PrestaShop\PrestaShop\Core\Form\EntityFormDataHandlerInterface;
use PrestaShop\PrestaShop\Core\Form\EntityFormFactoryInterface;
use PrestaShop\PrestaShop\Core\Form\NumericEntityIdentifier;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class EntityController handles most common entity actions
 */
class EntityController extends FrameworkBundleAdminController
{
    /**
     * Create new entity
     *
     * @param Request $request
     * @param EntityFormFactoryInterface $entityFormFactory
     * @param EntityFormDataHandlerInterface $entityFormDataHandler
     *
     * @return Response
     */
    public function createAction(
        Request $request,
        EntityFormFactoryInterface $entityFormFactory,
        EntityFormDataHandlerInterface $entityFormDataHandler
    ) {
        $entityForm = $entityFormFactory->getForm();
        $entityForm->handleRequest($request);

        if ($entityForm->isSubmitted()) {
            $errors = $entityFormDataHandler->createEntity($entityForm->getData());

            if (empty($errors)) {
                $this->addFlash('success', $this->trans('Successful creation.', 'Admin.Notifications.Success'));

                return $this->redirectToRoute(
                    $request->attributes->get('redirect_after_create_route')
                );
            }

            $this->flashErrors($errors);
        }

        return $this->render($request->attributes->get('template'), [
            'entityForm' => $entityForm,
        ]);
    }

    /**
     * Update existing entity
     *
     * @param int $entityId
     * @param Request $request
     * @param EntityFormFactoryInterface $entityFormFactory
     * @param EntityFormDataHandlerInterface $entityFormDataHandler
     *
     * @return Response
     */
    public function editAction(
        $entityId,
        Request $request,
        EntityFormFactoryInterface $entityFormFactory,
        EntityFormDataHandlerInterface $entityFormDataHandler
    ) {
        $entityIdentifier = new NumericEntityIdentifier($entityId);

        $entityForm = $entityFormFactory->getFormFor($entityIdentifier);
        $entityForm->handleRequest($request);

        if ($entityForm->isSubmitted()) {
            $errors = $entityFormDataHandler->updateEntity(
                $entityIdentifier,
                $entityForm->getData()
            );

            if (empty($errors)) {
                $this->addFlash('success', $this->trans('Successful update.', 'Admin.Notifications.Success'));

                return $this->redirectToRoute(
                    $request->attributes->get('redirect_after_update_route')
                );
            }

            $this->flashErrors($errors);
        }

        return $this->render($request->attributes->get('template'), [
            'entityForm' => $entityForm,
        ]);
    }
}
