<?php
/**
 * 2007-2018 PrestaShop.
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

namespace PrestaShopBundle\Controller\Admin\Sell\Catalog;

use PrestaShop\PrestaShop\Core\Domain\Exception\DomainException;
use PrestaShop\PrestaShop\Core\Domain\Manufacturer\Exception\ManufacturerImageUploadingException;
use PrestaShop\PrestaShop\Core\Domain\Manufacturer\Exception\ManufacturerNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Manufacturer\Query\GetManufacturerForEditing;
use PrestaShop\PrestaShop\Core\Domain\Manufacturer\QueryResult\EditableManufacturer;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\Builder\FormBuilderInterface;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\Handler\FormHandlerInterface;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use PrestaShopBundle\Security\Annotation\AdminSecurity;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Is responsible for "Sell > Catalog > Brands & Suppliers" page.
 */
class ManufacturerController extends FrameworkBundleAdminController
{
    /**
     * Show & process manufacturer creation.
     *
     * @AdminSecurity(
     *     "is_granted(['update'], request.get('_legacy_controller'))"
     * )
     *
     * @param Request $request
     *
     * @return Response
     */
    public function createAction(Request $request)
    {
        try {
            $manufacturerForm = $this->getFormBuilder()->getForm();
            $manufacturerForm->handleRequest($request);

            $result = $this->getFormHandler()->handle($manufacturerForm);

            if (null !== $result->getIdentifiableObjectId()) {
                $this->addFlash('success', $this->trans('Successful update.', 'Admin.Notifications.Success'));

                return $this->redirectToRoute('admin_manufacturers_index');
            }
        } catch (DomainException $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages()));
        }

        return $this->render('@PrestaShop/Admin/Sell/Catalog/Manufacturer/add.html.twig', [
            'help_link' => $this->generateSidebarLink($request->attributes->get('_legacy_controller')),
            'enableSidebar' => true,
            'manufacturerForm' => $manufacturerForm->createView(),
        ]);
    }

    /**
     * Show & process manufacturer editing.
     *
     * @AdminSecurity(
     *     "is_granted(['update'], request.get('_legacy_controller'))"
     * )
     *
     * @param int $manufacturerId
     * @param Request $request
     *
     * @return Response
     */
    public function editAction(Request $request, $manufacturerId)
    {
        try {
            $manufacturerForm = $this->getFormBuilder()->getFormFor((int) $manufacturerId);
            $manufacturerForm->handleRequest($request);

            $result = $this->getFormHandler()->handleFor((int) $manufacturerId, $manufacturerForm);

            if (null !== $result->getIdentifiableObjectId()) {
                $this->addFlash('success', $this->trans('Successful update.', 'Admin.Notifications.Success'));

                return $this->redirectToRoute('admin_manufacturers_index');
            }
        } catch (DomainException $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages()));

            if ($e instanceof ManufacturerNotFoundException) {
                return $this->redirectToRoute('admin_manufacturers_index');
            }
        }
        /** @var EditableManufacturer $editableManufacturer */
        $editableManufacturer = $this->getQueryBus()->handle(new GetManufacturerForEditing((int) $manufacturerId));

        return $this->render('@PrestaShop/Admin/Sell/Catalog/Manufacturer/edit.html.twig', [
            'help_link' => $this->generateSidebarLink($request->attributes->get('_legacy_controller')),
            'enableSidebar' => true,
            'manufacturerForm' => $manufacturerForm->createView(),
            'manufacturerName' => $editableManufacturer->getName(),
            'logoImage' => $editableManufacturer->getLogoImage(),
        ]);
    }

    /**
     * Provides error messages for exceptions
     *
     * @return array
     */
    private function getErrorMessages()
    {
        return [
            ManufacturerNotFoundException::class => $this->trans(
                'The object cannot be loaded (or found)',
                'Admin.Notifications.Error'
            ),
            ManufacturerImageUploadingException::class => [
                ManufacturerImageUploadingException::MEMORY_LIMIT_RESTRICTION => $this->trans(
                    'Due to memory limit restrictions, this image cannot be loaded. Please increase your memory_limit value via your server\'s configuration settings. ',
                    'Admin.Notifications.Error'
                ),
                ManufacturerImageUploadingException::UNEXPECTED_ERROR => $this->trans(
                    'An error occurred while uploading the image.',
                    'Admin.Notifications.Error'
                ),
            ],
        ];
    }

    /**
     * @return FormHandlerInterface
     */
    private function getFormHandler()
    {
        return $this->get('prestashop.core.form.identifiable_object.handler.manufacturer_form_handler');
    }

    /**
     * @return FormBuilderInterface
     */
    private function getFormBuilder()
    {
        return $this->get('prestashop.core.form.identifiable_object.builder.manufacturer_form_builder');
    }
}
