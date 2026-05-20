<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\Controller\Admin\Configure\ShopParameters;

use Exception;
use PrestaShop\PrestaShop\Core\Domain\Alias\Command\BulkDeleteSearchTermsAliasesCommand;
use PrestaShop\PrestaShop\Core\Domain\Alias\Command\DeleteSearchTermAliasesCommand;
use PrestaShop\PrestaShop\Core\Domain\Alias\Exception\AliasConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Alias\Exception\AliasException;
use PrestaShop\PrestaShop\Core\Domain\Alias\Exception\AliasNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Alias\Exception\CannotDeleteAliasException;
use PrestaShop\PrestaShop\Core\Domain\Alias\Query\GetAliasesBySearchTermForEditing;
use PrestaShop\PrestaShop\Core\Domain\Alias\QueryResult\AliasForEditing;
use PrestaShop\PrestaShop\Core\Domain\Search\Command\SearchIndexationCommand;
use PrestaShop\PrestaShop\Core\Domain\Search\Query\GetIndexedProductsCount;
use PrestaShop\PrestaShop\Core\Form\FormHandlerInterface;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\Builder\FormBuilderInterface;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\Handler\FormHandlerInterface as IdentifiableFormHandlerInterface;
use PrestaShop\PrestaShop\Core\Grid\GridFactoryInterface;
use PrestaShop\PrestaShop\Core\Search\Filters\AliasFilters;
use PrestaShopBundle\Controller\Admin\PrestaShopAdminController;
use PrestaShopBundle\Security\Attribute\AdminSecurity;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Controller responsible for "Configure > Shop Parameters > Search" page.
 */
class SearchAliasController extends PrestaShopAdminController
{
    #[AdminSecurity("is_granted('read', request.get('_legacy_controller'))")]
    public function indexAction(
        Request $request,
        AliasFilters $filters,
        #[Autowire(service: 'prestashop.core.grid.factory.alias')]
        GridFactoryInterface $aliasGridFactory,
    ): Response {
        $aliasGrid = $aliasGridFactory->getGrid($filters);

        return $this->render('@PrestaShop/Admin/Configure/ShopParameters/Search/index.html.twig', [
            'aliasGrid' => $this->presentGrid($aliasGrid),
            'help_link' => $this->generateSidebarLink('AdminAliases'),
            'layoutHeaderToolbarBtn' => [
                'add' => [
                    'desc' => $this->trans('Add new alias', [], 'Admin.Shopparameters.Feature'),
                    'icon' => 'add_circle_outline',
                    'href' => $this->generateUrl('admin_search_alias_create'),
                ],
            ],
        ]);
    }

    #[AdminSecurity("is_granted('read', request.get('_legacy_controller'))")]
    public function preferencesIndexAction(
        #[Autowire(service: 'prestashop.admin.search_preferences.indexation.form_handler')]
        FormHandlerInterface $indexationFormHandler,
        #[Autowire(service: 'prestashop.admin.search_preferences.search_options.form_handler')]
        FormHandlerInterface $searchOptionsFormHandler,
        #[Autowire(service: 'prestashop.admin.search_preferences.weight.form_handler')]
        FormHandlerInterface $weightFormHandler,
    ): Response {
        $indexedCount = $this->dispatchQuery(new GetIndexedProductsCount());

        return $this->render('@PrestaShop/Admin/Configure/ShopParameters/Search/preferences.html.twig', [
            'help_link' => $this->generateSidebarLink('AdminSearchConf'),
            'indexationForm' => $indexationFormHandler->getForm()->createView(),
            'searchOptionsForm' => $searchOptionsFormHandler->getForm()->createView(),
            'weightForm' => $weightFormHandler->getForm()->createView(),
            'indexedProductsCount' => $indexedCount->getIndexed(),
            'totalProductsCount' => $indexedCount->getTotal(),
        ]);
    }

    #[AdminSecurity("is_granted('update', request.get('_legacy_controller')) && is_granted('create', request.get('_legacy_controller')) && is_granted('delete', request.get('_legacy_controller'))", message: 'You do not have permission to update this.', redirectRoute: 'admin_search_preferences_index')]
    public function processIndexationFormAction(
        Request $request,
        #[Autowire(service: 'prestashop.admin.search_preferences.indexation.form_handler')]
        FormHandlerInterface $formHandler,
    ): RedirectResponse {
        return $this->processForm($request, $formHandler, 'Indexation');
    }

    #[AdminSecurity("is_granted('update', request.get('_legacy_controller')) && is_granted('create', request.get('_legacy_controller')) && is_granted('delete', request.get('_legacy_controller'))", message: 'You do not have permission to update this.', redirectRoute: 'admin_search_preferences_index')]
    public function processSearchOptionsFormAction(
        Request $request,
        #[Autowire(service: 'prestashop.admin.search_preferences.search_options.form_handler')]
        FormHandlerInterface $formHandler,
    ): RedirectResponse {
        return $this->processForm($request, $formHandler, 'SearchOptions');
    }

    #[AdminSecurity("is_granted('update', request.get('_legacy_controller')) && is_granted('create', request.get('_legacy_controller')) && is_granted('delete', request.get('_legacy_controller'))", message: 'You do not have permission to update this.', redirectRoute: 'admin_search_preferences_index')]
    public function processWeightFormAction(
        Request $request,
        #[Autowire(service: 'prestashop.admin.search_preferences.weight.form_handler')]
        FormHandlerInterface $formHandler,
    ): RedirectResponse {
        return $this->processForm($request, $formHandler, 'Weight');
    }

    #[AdminSecurity("is_granted('update', request.get('_legacy_controller')) && is_granted('create', request.get('_legacy_controller')) && is_granted('delete', request.get('_legacy_controller'))", message: 'You do not have permission to update this.', redirectRoute: 'admin_search_preferences_index')]
    public function addMissingToIndexAction(): RedirectResponse
    {
        try {
            $this->dispatchCommand(new SearchIndexationCommand(false));
            $this->addFlash('success', $this->trans('The index was successfully updated.', [], 'Admin.Shopparameters.Notification'));
        } catch (Exception $e) {
            $this->addFlash('error', $this->trans('An error occurred while indexing products.', [], 'Admin.Shopparameters.Notification'));
        }

        return $this->redirectToRoute('admin_search_preferences_index');
    }

    #[AdminSecurity("is_granted('update', request.get('_legacy_controller')) && is_granted('create', request.get('_legacy_controller')) && is_granted('delete', request.get('_legacy_controller'))", message: 'You do not have permission to update this.', redirectRoute: 'admin_search_preferences_index')]
    public function rebuildIndexAction(): RedirectResponse
    {
        try {
            $this->dispatchCommand(new SearchIndexationCommand(true));
            $this->addFlash('success', $this->trans('The index was successfully rebuilt.', [], 'Admin.Shopparameters.Notification'));
        } catch (Exception $e) {
            $this->addFlash('error', $this->trans('An error occurred while rebuilding the index.', [], 'Admin.Shopparameters.Notification'));
        }

        return $this->redirectToRoute('admin_search_preferences_index');
    }

    public function cronIndexationAction(): Response
    {
        try {
            $this->dispatchCommand(new SearchIndexationCommand(true));
        } catch (Exception $e) {
            return new Response('Indexation failed: ' . $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return new Response('OK');
    }

    #[AdminSecurity("is_granted('create', request.get('_legacy_controller'))", redirectRoute: 'admin_search_alias_index', message: 'You need permission to create new aliases.')]
    public function createAction(
        Request $request,
        #[Autowire(service: 'prestashop.core.form.identifiable_object.builder.alias_search_term_form_builder')]
        FormBuilderInterface $formBuilder,
        #[Autowire(service: 'prestashop.core.form.identifiable_object.builder.alias_search_term_form_handler')]
        IdentifiableFormHandlerInterface $formHandler,
    ): Response {
        $form = $formBuilder->getForm();
        $form->handleRequest($request);

        try {
            $formHandlerResult = $formHandler->handle($form);

            if (null !== $formHandlerResult->getIdentifiableObjectId()) {
                $this->addFlash('success', $this->trans('Successful creation.', [], 'Admin.Notifications.Success'));

                return $this->redirectToRoute('admin_search_alias_index');
            }
        } catch (Exception $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages($e)));
        }

        return $this->render('@PrestaShop/Admin/Configure/ShopParameters/Search/form.html.twig', [
            'form' => $form->createView(),
            'help_link' => $this->generateSidebarLink('AdminSearchConf'),
            'enableSidebar' => true,
            'layoutTitle' => $this->trans('New aliases', [], 'Admin.Shopparameters.Feature'),
        ]);
    }

    #[AdminSecurity("is_granted('update', request.get('_legacy_controller'))", redirectRoute: 'admin_search_alias_index', message: 'You need permission to edit this.')]
    public function editAction(
        string $searchTerm,
        Request $request,
        #[Autowire(service: 'prestashop.core.form.identifiable_object.builder.alias_search_term_form_builder')]
        FormBuilderInterface $formBuilder,
        #[Autowire(service: 'prestashop.core.form.identifiable_object.builder.alias_search_term_form_handler')]
        IdentifiableFormHandlerInterface $formHandler,
    ): Response {
        try {
            /**
             * @var AliasForEditing $editableAlias
             */
            $editableAlias = $this->dispatchQuery(new GetAliasesBySearchTermForEditing($searchTerm));

            $form = $formBuilder->getFormFor($searchTerm);
            $form->handleRequest($request);
            $result = $formHandler->handleFor($searchTerm, $form);

            if ($result->isSubmitted() && $result->isValid()) {
                $this->addFlash('success', $this->trans('Successful update', [], 'Admin.Notifications.Success'));

                return $this->redirectToRoute('admin_search_alias_index');
            }
        } catch (Exception $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages($e)));
        }

        return $this->render('@PrestaShop/Admin/Configure/ShopParameters/Search/form.html.twig', [
            'form' => $form->createView(),
            'help_link' => $this->generateSidebarLink('AdminSearchConf'),
            'enableSidebar' => true,
            'layoutTitle' => $this->trans(
                'Edit aliases for %s',
                [
                    $editableAlias->getSearchTerm(),
                ],
                'Admin.Shopparameters.Feature'
            ),
        ]);
    }

    #[AdminSecurity("is_granted('delete', request.get('_legacy_controller'))", redirectRoute: 'admin_search_alias_index')]
    public function deleteAction(string $searchTerm): RedirectResponse
    {
        try {
            $this->dispatchCommand(new DeleteSearchTermAliasesCommand($searchTerm));
            $this->addFlash('success', $this->trans('Successful deletion.', [], 'Admin.Notifications.Success'));
        } catch (AliasException $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages($e)));

            return $this->redirectToRoute('admin_search_alias_index');
        }

        return $this->redirectToRoute('admin_search_alias_index');
    }

    #[AdminSecurity("is_granted('delete', request.get('_legacy_controller'))", redirectRoute: 'admin_search_alias_index')]
    public function bulkDeleteAction(Request $request): RedirectResponse
    {
        $searchTerms = $request->request->all('alias_term_bulk');

        try {
            $this->dispatchCommand(new BulkDeleteSearchTermsAliasesCommand($searchTerms));

            $this->addFlash(
                'success',
                $this->trans('The selection has been successfully deleted.', [], 'Admin.Notifications.Success')
            );
        } catch (AliasException $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages($e)));
        }

        return $this->redirectToRoute('admin_search_alias_index');
    }

    private function processForm(Request $request, FormHandlerInterface $formHandler, string $hookName): RedirectResponse
    {
        $this->dispatchHookWithParameters(
            'actionAdminShopParametersSearchPreferencesControllerPostProcess' . $hookName . 'Before',
            ['controller' => $this]
        );

        $form = $formHandler->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $data = $form->getData();
            $saveErrors = $formHandler->save($data);

            if (0 === count($saveErrors)) {
                $this->addFlash('success', $this->trans('Update successful', [], 'Admin.Notifications.Success'));
            } else {
                $this->addFlashErrors($saveErrors);
            }
        }

        return $this->redirectToRoute('admin_search_preferences_index');
    }

    public function getErrorMessages(Exception $e): array
    {
        return [
            AliasConstraintException::class => [
                AliasConstraintException::INVALID_ID => $this->trans(
                    'Invalid alias ID.',
                    [],
                    'Admin.Shopparameters.Feature'
                ),
                AliasConstraintException::INVALID_ALIAS => $this->trans(
                    'Invalid alias.',
                    [],
                    'Admin.Shopparameters.Feature'
                ),
                AliasConstraintException::INVALID_SEARCH => $this->trans(
                    'Invalid search term.',
                    [],
                    'Admin.Shopparameters.Feature'
                ),
                AliasConstraintException::ALIAS_ALREADY_USED => $this->trans(
                    'Some alias are already in use for another search term: %s.',
                    [$e->getMessage()],
                    'Admin.Shopparameters.Feature'
                ),
            ],
            AliasNotFoundException::class => $this->trans(
                'This alias does not exist.',
                [],
                'Admin.Shopparameters.Feature'
            ),
            CannotDeleteAliasException::class => $this->trans(
                'Failed to delete alias.',
                [],
                'Admin.Shopparameters.Feature'
            ),
        ];
    }
}
