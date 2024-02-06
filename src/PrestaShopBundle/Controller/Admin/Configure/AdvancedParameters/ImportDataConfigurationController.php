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

namespace PrestaShopBundle\Controller\Admin\Configure\AdvancedParameters;

use PrestaShop\PrestaShop\Core\Import\Exception\UnreadableFileException;
use PrestaShop\PrestaShop\Core\Import\ImportSettings;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use PrestaShopBundle\Security\Attribute\AdminSecurity;
use PrestaShopBundle\Security\Attribute\DemoRestricted;
use SplFileInfo;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Responsible of "Configure > Advanced Parameters > Import" step 2 page display.
 */
class ImportDataConfigurationController extends FrameworkBundleAdminController
{
    /**
     * Shows import data page where the configuration of importable data and the final step of import is handled.
     *
     * @param Request $request
     *
     * @return RedirectResponse|Response
     */
    #[DemoRestricted(redirectRoute: 'admin_import')]
    #[AdminSecurity("is_granted('read', request.get('_legacy_controller'))")]
    public function indexAction(Request $request)
    {
        $importDirectory = $this->get('prestashop.core.import.dir');
        $dataRowCollectionFactory = $this->get('prestashop.core.import.factory.data_row.collection_factory');
        $dataRowCollectionPresenter = $this->get('prestashop.core.import.data_row.collection_presenter');
        $entityFieldsProviderFinder = $this->get('prestashop.core.import.fields_provider_finder');
        $formHandler = $this->get('prestashop.admin.import_data_configuration.form_handler');
        $importConfigFactory = $this->get('prestashop.core.import.config_factory');

        $importFile = new SplFileInfo($importDirectory . $request->getSession()->get('csv'));
        $importConfig = $importConfigFactory->buildFromRequest($request);
        $form = $formHandler->getForm($importConfig);

        try {
            $dataRowCollection = $dataRowCollectionFactory->buildFromFile(
                $importFile,
                ImportSettings::MAX_VISIBLE_ROWS
            );
        } catch (UnreadableFileException $e) {
            $this->addFlash(
                'error',
                $this->trans('The import file cannot be read.', 'Admin.Advparameters.Notification')
            );

            return $this->redirectToRoute('admin_import');
        }

        $presentedDataRowCollection = $dataRowCollectionPresenter->present($dataRowCollection);
        $entityFieldsProvider = $entityFieldsProviderFinder->find($importConfig->getEntityType());

        return $this->render(
            '@PrestaShop/Admin/Configure/AdvancedParameters/ImportDataConfiguration/index.html.twig',
            [
                'importDataConfigurationForm' => $form->createView(),
                'dataRowCollection' => $presentedDataRowCollection,
                'maxVisibleColumns' => ImportSettings::MAX_VISIBLE_COLUMNS,
                'layoutTitle' => $this->trans('Import', 'Admin.Navigation.Menu'),
                'showPagingArrows' => $presentedDataRowCollection['row_size'] > ImportSettings::MAX_VISIBLE_COLUMNS,
                'requiredFields' => $entityFieldsProvider->getCollection()->getRequiredFields(),
            ]
        );
    }

    /**
     * Create import data match configuration.
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    #[DemoRestricted(redirectRoute: 'admin_import')]
    #[AdminSecurity("is_granted('update', request.get('_legacy_controller'))", message: 'You do not have permission to update this.', redirectRoute: 'admin_import')]
    public function createAction(Request $request)
    {
        $formHandler = $this->get('prestashop.admin.import_data_configuration.form_handler');
        $importConfigFactory = $this->get('prestashop.core.import.config_factory');

        $importConfig = $importConfigFactory->buildFromRequest($request);
        $form = $formHandler->getForm($importConfig);
        $form->setData([
            'match_name' => $request->request->get('match_name'),
            'skip' => $request->request->get('skip'),
            'type_value' => $request->request->get('type_value'),
        ]);

        $errors = $formHandler->save($form->getData());
        $matches = [];

        if (!$errors) {
            $importMatchRepository = $this->get('prestashop.core.admin.import_match.repository');
            $matches = $importMatchRepository->findAll();
        }

        return $this->json([
            'errors' => $errors,
            'matches' => $matches,
        ]);
    }

    /**
     * Delete import data match configuration.
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    #[DemoRestricted(redirectRoute: 'admin_import')]
    #[AdminSecurity("is_granted('update', request.get('_legacy_controller'))", message: 'You do not have permission to update this.', redirectRoute: 'admin_import')]
    public function deleteAction(Request $request)
    {
        $importMatchRepository = $this->get('prestashop.core.admin.import_match.repository');
        $importMatchRepository->deleteById($request->get('import_match_id'));

        return $this->json([]);
    }

    /**
     * Get import data match configuration.
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    #[AdminSecurity("is_granted('read', request.get('_legacy_controller'))", redirectRoute: 'admin_import')]
    public function getAction(Request $request)
    {
        $importMatchRepository = $this->get('prestashop.core.admin.import_match.repository');
        $importMatch = $importMatchRepository->findOneById($request->get('import_match_id'));

        return $this->json($importMatch);
    }
}
