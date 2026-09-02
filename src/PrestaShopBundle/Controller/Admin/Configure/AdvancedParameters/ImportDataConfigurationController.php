<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Controller\Admin\Configure\AdvancedParameters;

use PrestaShop\PrestaShop\Core\Import\Configuration\ImportConfigFactoryInterface;
use PrestaShop\PrestaShop\Core\Import\EntityField\Provider\EntityFieldsProviderFinder;
use PrestaShop\PrestaShop\Core\Import\Exception\UnreadableFileException;
use PrestaShop\PrestaShop\Core\Import\File\DataRow\DataRowCollectionPresenterInterface;
use PrestaShop\PrestaShop\Core\Import\File\DataRow\Factory\DataRowCollectionFactoryInterface;
use PrestaShop\PrestaShop\Core\Import\ImportDirectory;
use PrestaShop\PrestaShop\Core\Import\ImportSettings;
use PrestaShopBundle\Controller\Admin\PrestaShopAdminController;
use PrestaShopBundle\Entity\Repository\ImportMatchRepository;
use PrestaShopBundle\Form\Admin\Configure\AdvancedParameters\Import\ImportFormHandlerInterface;
use PrestaShopBundle\Security\Attribute\AdminSecurity;
use PrestaShopBundle\Security\Attribute\DemoRestricted;
use SplFileInfo;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Responsible for "Configure > Advanced Parameters > Import" step 2 page display.
 */
class ImportDataConfigurationController extends PrestaShopAdminController
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
    public function indexAction(
        Request $request,
        ImportDirectory $importDirectory,
        DataRowCollectionFactoryInterface $dataRowCollectionFactory,
        DataRowCollectionPresenterInterface $dataRowCollectionPresenter,
        EntityFieldsProviderFinder $entityFieldsProviderFinder,
        #[Autowire(service: 'prestashop.admin.import_data_configuration.form_handler')]
        ImportFormHandlerInterface $formHandler,
        ImportConfigFactoryInterface $importConfigFactory,
    ): Response|RedirectResponse {
        $importFile = new SplFileInfo($importDirectory . $request->getSession()->get('csv'));
        $importConfig = $importConfigFactory->buildFromRequest($request);
        $form = $formHandler->getForm($importConfig);

        try {
            $dataRowCollection = $dataRowCollectionFactory->buildFromFile(
                $importFile,
                ImportSettings::MAX_VISIBLE_ROWS
            );
        } catch (UnreadableFileException) {
            $this->addFlash(
                'error',
                $this->trans('The import file cannot be read.', [], 'Admin.Advparameters.Notification')
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
                'layoutTitle' => $this->trans('Import', [], 'Admin.Navigation.Menu'),
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
    public function createAction(
        Request $request,
        #[Autowire(service: 'prestashop.admin.import_data_configuration.form_handler')]
        ImportFormHandlerInterface $formHandler,
        ImportConfigFactoryInterface $importConfigFactory,
        ImportMatchRepository $importMatchRepository,
    ): JsonResponse {
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
    public function deleteAction(
        Request $request,
        ImportMatchRepository $importMatchRepository,
    ): JsonResponse {
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
    public function getAction(
        Request $request,
        ImportMatchRepository $importMatchRepository,
    ): JsonResponse {
        $importMatch = $importMatchRepository->findOneById($request->get('import_match_id'));

        return $this->json($importMatch);
    }
}
