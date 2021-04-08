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

namespace PrestaShopBundle\Controller\Admin\Configure\ShopParameters;

use PrestaShop\PrestaShop\Core\Form\FormHandlerInterface;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use PrestaShopBundle\Controller\Exception\FieldNotFoundException;
use PrestaShopBundle\Form\Admin\Configure\ShopParameters\ProductPreferences\GeneralFormDataProvider;
use PrestaShopBundle\Form\Admin\Configure\ShopParameters\ProductPreferences\GeneralType;
use PrestaShopBundle\Form\Exception\DataProviderException;
use PrestaShopBundle\Form\Exception\InvalidConfigurationDataError;
use PrestaShopBundle\Form\Exception\InvalidConfigurationDataErrorCollection;
use PrestaShopBundle\Security\Annotation\AdminSecurity;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Responsible of "Configure > Shop Parameters > Product Settings" page.
 */
class ProductPreferencesController extends FrameworkBundleAdminController
{
    /**
     * @param Request $request
     *
     * @AdminSecurity("is_granted('read', request.get('_legacy_controller'))")
     *
     * @return Response
     */
    public function indexAction(Request $request)
    {
        $legacyController = $request->attributes->get('_legacy_controller');

        $generalForm = $this->getGeneralFormHandler()->getForm();
        $pageForm = $this->getPageFormHandler()->getForm();
        $paginationForm = $this->getPaginationFormHandler()->getForm();
        $stockForm = $this->getStockFormHandler()->getForm();

        return $this->render('@PrestaShop/Admin/Configure/ShopParameters/product_preferences.html.twig', [
            'layoutHeaderToolbarBtn' => [],
            'layoutTitle' => $this->trans('Product Settings', 'Admin.Navigation.Menu'),
            'requireAddonsSearch' => true,
            'requireBulkAction' => false,
            'showContentHeader' => true,
            'enableSidebar' => true,
            'help_link' => $this->generateSidebarLink($legacyController),
            'requireFilterStatus' => false,
            'generalForm' => $generalForm->createView(),
            'pageForm' => $pageForm->createView(),
            'paginationForm' => $paginationForm->createView(),
            'stockForm' => $stockForm->createView(),
        ]);
    }

    /**
     * @AdminSecurity("is_granted(['update', 'create', 'delete'], request.get('_legacy_controller'))",
     *     message="You do not have permission to update this.",
     *     redirectRoute="admin_product_preferences"
     * )
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function processGeneralFormAction(Request $request)
    {
        return $this->processForm(
            $request,
            $this->getGeneralFormHandler(),
            'General'
        );
    }

    /**
     * @AdminSecurity("is_granted(['update', 'create', 'delete'], request.get('_legacy_controller'))",
     *     message="You do not have permission to update this.",
     *     redirectRoute="admin_product_preferences"
     * )
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function processPageFormAction(Request $request)
    {
        return $this->processForm(
            $request,
            $this->getPageFormHandler(),
            'Page'
        );
    }

    /**
     * @AdminSecurity("is_granted(['update', 'create', 'delete'], request.get('_legacy_controller'))",
     *     message="You do not have permission to update this.",
     *     redirectRoute="admin_product_preferences"
     * )
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function processPaginationFormAction(Request $request)
    {
        return $this->processForm(
            $request,
            $this->getPaginationFormHandler(),
            'Pagination'
        );
    }

    /**
     * @AdminSecurity("is_granted(['update', 'create', 'delete'], request.get('_legacy_controller'))",
     *     message="You do not have permission to update this.",
     *     redirectRoute="admin_product_preferences"
     * )
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function processStockFormAction(Request $request)
    {
        return $this->processForm(
            $request,
            $this->getStockFormHandler(),
            'Stock'
        );
    }

    /**
     * Process the Product Preferences configuration form.
     *
     * @param Request $request
     * @param FormHandlerInterface $formHandler
     * @param string $hookName
     *
     * @return RedirectResponse
     */
    protected function processForm(Request $request, FormHandlerInterface $formHandler, string $hookName)
    {
        $this->dispatchHook(
            'actionAdminShopParametersProductPreferencesControllerPostProcess' . $hookName . 'Before',
            ['controller' => $this]
        );

        $this->dispatchHook('actionAdminShopParametersProductPreferencesControllerPostProcessBefore', ['controller' => $this]);

        $form = $formHandler->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $data = $form->getData();
            try {
                $formHandler->save($data);
                $this->addFlash('success', $this->trans('Update successful', 'Admin.Notifications.Success'));

            } catch (DataProviderException $e) {
                $this->flashErrors($this->getErrorMessages($e->getInvalidConfigurationDataErrors()));
            }
        }

        return $this->redirectToRoute('admin_product_preferences');
    }

    /**
     * @var InvalidConfigurationDataErrorCollection
     */
    private function getErrorMessages(InvalidConfigurationDataErrorCollection $errors): array
    {
        $messages = [];

        foreach ($errors as $error) {
            $messages[] = $this->getErrorMessage($error);
        }

        return $messages;
    }

    /**
     * @param InvalidConfigurationDataError $error
     *
     * @return string
     *
     * @throws FieldNotFoundException
     */
    private function getErrorMessage(InvalidConfigurationDataError $error): string
    {
        switch ($error->getErrorCode()) {
            case GeneralFormDataProvider::ERROR_MUST_BE_NUMERIC_EQUAL_TO_ZERO_OR_HIGHER:
                return $this->trans(
                    '%s is invalid. Please enter an integer greater than or equal to 0.',
                    'Admin.Notifications.Error',
                    [$this->getFieldLabel($error->getFieldName())]
                );
        }

        return $this->trans(
            'The %s field is invalid.',
            'Admin.Notifications.Error',
            [
                $this->getFieldLabel($error->getFieldName()),
            ]
        );
    }

    /**
     * @param string $fieldName
     *
     * @return string
     */
    private function getFieldLabel(string $fieldName): string
    {
        switch ($fieldName) {
            case GeneralType::FIELD_SHORT_DESCRIPTION_LIMIT:
                return $this->trans(
                    'Max size of product summary',
                    'Admin.Shopparameters.Feature'
                );
        }

        throw new FieldNotFoundException(
            sprintf(
                'Field name for field %s not found',
                $fieldName
            )
        );
    }

    /**
     * @return FormHandlerInterface
     */
    protected function getGeneralFormHandler(): FormHandlerInterface
    {
        return $this->get('prestashop.admin.product_preferences.general.form_handler');
    }

    /**
     * @return FormHandlerInterface
     */
    protected function getPaginationFormHandler(): FormHandlerInterface
    {
        return $this->get('prestashop.admin.product_preferences.pagination.form_handler');
    }

    /**
     * @return FormHandlerInterface
     */
    protected function getPageFormHandler(): FormHandlerInterface
    {
        return $this->get('prestashop.admin.product_preferences.page.form_handler');
    }

    /**
     * @return FormHandlerInterface
     */
    protected function getStockFormHandler(): FormHandlerInterface
    {
        return $this->get('prestashop.admin.product_preferences.stock.form_handler');
    }
}
