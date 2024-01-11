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

use Exception;
use PrestaShop\PrestaShop\Adapter\Tools;
use PrestaShop\PrestaShop\Core\Domain\Meta\Exception\MetaConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Meta\Exception\MetaNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\ShowcaseCard\Query\GetShowcaseCardIsClosed;
use PrestaShop\PrestaShop\Core\Domain\ShowcaseCard\ValueObject\ShowcaseCard;
use PrestaShop\PrestaShop\Core\Form\FormHandlerInterface;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\Builder\FormBuilderInterface;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\Handler;
use PrestaShop\PrestaShop\Core\Search\Filters\MetaFilters;
use PrestaShop\PrestaShop\Core\Util\HelperCard\DocumentationLinkProviderInterface;
use PrestaShop\PrestaShop\Core\Util\Url\UrlFileCheckerInterface;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use PrestaShopBundle\Security\Annotation\AdminSecurity;
use PrestaShopBundle\Security\Annotation\DemoRestricted;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class MetaController is responsible for page display and all actions used in Configure -> Shop parameters ->
 * Traffic & Seo -> Seo & Urls tab.
 */
class MetaController extends FrameworkBundleAdminController
{
    /**
     * Shows index Meta page.
     *
     * @AdminSecurity("is_granted('read', request.get('_legacy_controller'))")
     *
     * @param MetaFilters $filters
     * @param Request $request
     *
     * @return Response
     */
    public function indexAction(MetaFilters $filters, Request $request)
    {
        $setUpUrlsForm = $this->getSetUpUrlsFormHandler()->getForm();
        $shopUrlsForm = $this->getShopUrlsFormHandler()->getForm();
        $seoOptionsForm = $this->getSeoOptionsFormHandler()->getForm();
        $isRewriteSettingEnabled = $this->getConfiguration()->getBoolean('PS_REWRITING_SETTINGS');

        $urlSchemaForm = null;
        if ($isRewriteSettingEnabled) {
            $urlSchemaForm = $this->getUrlSchemaFormHandler()->getForm();
        }

        return $this->doRenderForm($request, $filters, $setUpUrlsForm, $shopUrlsForm, $seoOptionsForm, $urlSchemaForm);
    }

    /**
     * Points to the form where new record of meta list can be created.
     *
     * @AdminSecurity("is_granted('create', request.get('_legacy_controller'))", message="You do not have permission to add this.")
     *
     * @param Request $request
     *
     * @return Response
     */
    public function createAction(Request $request)
    {
        $data = [];
        $metaForm = $this->getMetaFormBuilder()->getForm($data);
        $metaForm->handleRequest($request);

        try {
            $result = $this->getMetaFormHandler()->handle($metaForm);

            if (null !== $result->getIdentifiableObjectId()) {
                $this->addFlash('success', $this->trans('Successful creation', 'Admin.Notifications.Success'));

                return $this->redirectToRoute('admin_metas_index');
            }
        } catch (Exception $exception) {
            $this->addFlash('error', $this->handleException($exception));
        }

        return $this->render('@PrestaShop/Admin/Configure/ShopParameters/TrafficSeo/Meta/create.html.twig', [
            'meta_form' => $metaForm->createView(),
            'multistoreInfoTip' => $this->trans(
                'Note that this feature is only available in the "all stores" context. It will be added to all your stores.',
                'Admin.Notifications.Info'
            ),
            'multistoreIsUsed' => $this->get('prestashop.adapter.multistore_feature')->isUsed(),
            'layoutTitle' => $this->trans('New page configuration', 'Admin.Navigation.Menu'),
        ]
        );
    }

    /**
     * Redirects to page where list record can be edited.
     *
     * @AdminSecurity("is_granted('update', request.get('_legacy_controller'))", message="You do not have permission to edit this.")
     *
     * @param int $metaId
     * @param Request $request
     *
     * @return Response
     */
    public function editAction($metaId, Request $request)
    {
        try {
            $metaForm = $this->getMetaFormBuilder()->getFormFor($metaId);
            $metaForm->handleRequest($request);

            $result = $this->getMetaFormHandler()->handleFor($metaId, $metaForm);

            if ($result->isSubmitted() && $result->isValid()) {
                $this->addFlash('success', $this->trans('Successful update', 'Admin.Notifications.Success'));

                return $this->redirectToRoute('admin_metas_index');
            }
        } catch (Exception $e) {
            $this->addFlash('error', $this->handleException($e));

            return $this->redirectToRoute('admin_metas_index');
        }

        return $this->render('@PrestaShop/Admin/Configure/ShopParameters/TrafficSeo/Meta/edit.html.twig', [
            'meta_form' => $metaForm->createView(),
            'layoutTitle' => $this->trans(
                'Editing configuration for %name%',
                'Admin.Navigation.Menu',
                [
                    '%name%' => $metaForm->getData()['page_name'],
                ]
            ),
        ]
        );
    }

    /**
     * Removes single element from meta list.
     *
     * @AdminSecurity("is_granted('delete', request.get('_legacy_controller'))", message="You do not have permission to delete this.")
     *
     * @param int $metaId
     *
     * @return RedirectResponse
     */
    #[DemoRestricted(redirectRoute: 'admin_metas_index')]
    public function deleteAction($metaId)
    {
        $metaEraser = $this->get('prestashop.adapter.meta.meta_eraser');
        $errors = $metaEraser->erase([$metaId]);

        if (!empty($errors)) {
            $this->flashErrors($errors);
        } else {
            $this->addFlash(
                'success',
                $this->trans('Successful deletion', 'Admin.Notifications.Success')
            );
        }

        return $this->redirectToRoute('admin_metas_index');
    }

    /**
     * Removes multiple records from meta list.
     *
     * @AdminSecurity("is_granted('delete', request.get('_legacy_controller'))", message="You do not have permission to delete this.")
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    #[DemoRestricted(redirectRoute: 'admin_metas_index')]
    public function deleteBulkAction(Request $request)
    {
        $metaToDelete = $request->request->all('meta_bulk');

        $metaEraser = $this->get('prestashop.adapter.meta.meta_eraser');
        $errors = $metaEraser->erase($metaToDelete);

        if (!empty($errors)) {
            $this->flashErrors($errors);
        } else {
            $this->addFlash(
                'success',
                $this->trans('The selection has been successfully deleted.', 'Admin.Notifications.Success')
            );
        }

        return $this->redirectToRoute('admin_metas_index');
    }

    /**
     * @AdminSecurity("is_granted('update', request.get('_legacy_controller'))", message="You do not have permission to edit this.")
     *
     * @param MetaFilters $filters
     * @param Request $request
     *
     * @return Response|RedirectResponse
     */
    #[DemoRestricted(redirectRoute: 'admin_metas_index')]
    public function processSetUpUrlsFormAction(MetaFilters $filters, Request $request)
    {
        $formProcessResult = $this->processForm(
            $request,
            $this->getSetUpUrlsFormHandler(),
            'SetUpUrls'
        );

        if ($formProcessResult instanceof RedirectResponse) {
            return $formProcessResult;
        }

        $shopUrlsForm = $this->getShopUrlsFormHandler()->getForm();
        $seoOptionsForm = $this->getSeoOptionsFormHandler()->getForm();
        $isRewriteSettingEnabled = $this->getConfiguration()->getBoolean('PS_REWRITING_SETTINGS');

        $urlSchemaForm = null;
        if ($isRewriteSettingEnabled) {
            $urlSchemaForm = $this->getUrlSchemaFormHandler()->getForm();
        }

        return $this->doRenderForm($request, $filters, $formProcessResult, $shopUrlsForm, $seoOptionsForm, $urlSchemaForm);
    }

    /**
     * @AdminSecurity("is_granted('update', request.get('_legacy_controller'))", message="You do not have permission to edit this.")
     *
     * @param MetaFilters $filters
     * @param Request $request
     *
     * @return Response|RedirectResponse
     */
    #[DemoRestricted(redirectRoute: 'admin_metas_index')]
    public function processShopUrlsFormAction(MetaFilters $filters, Request $request)
    {
        $formProcessResult = $this->processForm(
            $request,
            $this->getShopUrlsFormHandler(),
            'ShopUrls'
        );

        if ($formProcessResult instanceof RedirectResponse) {
            return $formProcessResult;
        }

        $setUpUrlsForm = $this->getSetUpUrlsFormHandler()->getForm();
        $seoOptionsForm = $this->getSeoOptionsFormHandler()->getForm();
        $isRewriteSettingEnabled = $this->getConfiguration()->getBoolean('PS_REWRITING_SETTINGS');

        $urlSchemaForm = null;
        if ($isRewriteSettingEnabled) {
            $urlSchemaForm = $this->getUrlSchemaFormHandler()->getForm();
        }

        return $this->doRenderForm($request, $filters, $setUpUrlsForm, $formProcessResult, $seoOptionsForm, $urlSchemaForm);
    }

    /**
     * @AdminSecurity("is_granted('update', request.get('_legacy_controller'))", message="You do not have permission to edit this.")
     *
     * @param MetaFilters $filters
     * @param Request $request
     *
     * @return Response|RedirectResponse
     */
    #[DemoRestricted(redirectRoute: 'admin_metas_index')]
    public function processUrlSchemaFormAction(MetaFilters $filters, Request $request)
    {
        $formProcessResult = $this->processForm(
            $request,
            $this->getUrlSchemaFormHandler(),
            'UrlSchema'
        );

        if ($formProcessResult instanceof RedirectResponse) {
            return $formProcessResult;
        }

        $setUpUrlsForm = $this->getSetUpUrlsFormHandler()->getForm();
        $shopUrlsForm = $this->getShopUrlsFormHandler()->getForm();
        $seoOptionsForm = $this->getSeoOptionsFormHandler()->getForm();

        return $this->doRenderForm($request, $filters, $setUpUrlsForm, $shopUrlsForm, $seoOptionsForm, $formProcessResult);
    }

    /**
     * @AdminSecurity("is_granted('update', request.get('_legacy_controller'))", message="You do not have permission to edit this.")
     *
     * @param MetaFilters $filters
     * @param Request $request
     *
     * @return Response|RedirectResponse
     */
    #[DemoRestricted(redirectRoute: 'admin_metas_index')]
    public function processSeoOptionsFormAction(MetaFilters $filters, Request $request)
    {
        $formProcessResult = $this->processForm(
            $request,
            $this->getSeoOptionsFormHandler(),
            'SeoOptions'
        );

        if ($formProcessResult instanceof RedirectResponse) {
            return $formProcessResult;
        }

        $setUpUrlsForm = $this->getSetUpUrlsFormHandler()->getForm();
        $shopUrlsForm = $this->getShopUrlsFormHandler()->getForm();
        $isRewriteSettingEnabled = $this->getConfiguration()->getBoolean('PS_REWRITING_SETTINGS');

        $urlSchemaForm = null;
        if ($isRewriteSettingEnabled) {
            $urlSchemaForm = $this->getUrlSchemaFormHandler()->getForm();
        }

        return $this->doRenderForm($request, $filters, $setUpUrlsForm, $shopUrlsForm, $formProcessResult, $urlSchemaForm);
    }

    /**
     * Generates robots.txt file for Front Office.
     *
     * @AdminSecurity(
     *     "is_granted('create', request.get('_legacy_controller')) && is_granted('update', request.get('_legacy_controller')) && is_granted('delete', request.get('_legacy_controller'))"
     * )
     *
     * @return RedirectResponse
     */
    #[DemoRestricted(redirectRoute: 'admin_metas_index')]
    public function generateRobotsFileAction()
    {
        $robotsTextFileGenerator = $this->get('prestashop.adapter.file.robots_text_file_generator');

        $rootDir = $this->getConfiguration()->get('_PS_ROOT_DIR_');

        if (!$robotsTextFileGenerator->generateFile()) {
            $this->addFlash(
                'error',
                $this->trans(
                    'Cannot write into file: %filename%. Please check write permissions.',
                    'Admin.Notifications.Error',
                    [
                        '%filename%' => $rootDir . '/robots.txt',
                    ]
                )
            );

            return $this->redirectToRoute('admin_metas_index');
        }

        $this->addFlash(
            'success',
            $this->trans('Successful update', 'Admin.Notifications.Success')
        );

        return $this->redirectToRoute('admin_metas_index');
    }

    /**
     * @param Request $request
     * @param MetaFilters $filters
     * @param FormInterface $setUpUrlsForm
     * @param FormInterface $shopUrlsForm
     * @param FormInterface $seoOptionsForm
     * @param FormInterface|null $urlSchemaForm
     *
     * @return Response
     */
    private function doRenderForm(
        Request $request,
        MetaFilters $filters,
        FormInterface $setUpUrlsForm,
        FormInterface $shopUrlsForm,
        FormInterface $seoOptionsForm,
        ?FormInterface $urlSchemaForm = null
    ): Response {
        $seoUrlsGridFactory = $this->get('prestashop.core.grid.factory.meta');

        $context = $this->get('prestashop.adapter.shop.context');

        $isShopContext = $context->isShopContext();
        $isShopFeatureActive = $this->get('prestashop.adapter.multistore_feature')->isActive();

        $isGridDisplayed = !($isShopFeatureActive && !$isShopContext);

        $presentedGrid = null;
        if ($isGridDisplayed) {
            $grid = $seoUrlsGridFactory->getGrid($filters);

            $presentedGrid = $this->presentGrid($grid);
        }

        $tools = $this->get(Tools::class);
        $urlFileChecker = $this->get(UrlFileCheckerInterface::class);
        $hostingInformation = $this->get('prestashop.adapter.hosting_information');
        $defaultRoutesProvider = $this->get('prestashop.adapter.data_provider.default_route');
        $helperBlockLinkProvider = $this->get(DocumentationLinkProviderInterface::class);
        $metaDataProvider = $this->get('prestashop.adapter.meta.data_provider');

        $showcaseCardIsClosed = $this->getQueryBus()->handle(
            new GetShowcaseCardIsClosed((int) $this->getContext()->employee->id, ShowcaseCard::SEO_URLS_CARD)
        );

        $doesMainShopUrlExist = $this->get('prestashop.adapter.shop.shop_url')->doesMainShopUrlExist();

        return $this->render(
            '@PrestaShop/Admin/Configure/ShopParameters/TrafficSeo/Meta/index.html.twig',
            [
                'layoutHeaderToolbarBtn' => [
                    'add' => [
                        'href' => $this->generateUrl('admin_metas_create'),
                        'desc' => $this->trans('Set up a new page', 'Admin.Shopparameters.Feature'),
                        'icon' => 'add_circle_outline',
                    ],
                ],
                'grid' => $presentedGrid,
                'setUpUrlsForm' => $setUpUrlsForm->createView(),
                'shopUrlsForm' => $shopUrlsForm->createView(),
                'urlSchemaForm' => $urlSchemaForm !== null ? $urlSchemaForm->createView() : null,
                'seoOptionsForm' => $seoOptionsForm->createView(),
                'robotsForm' => $this->createFormBuilder()->getForm()->createView(),
                'routeKeywords' => $defaultRoutesProvider->getKeywords(),
                'isGridDisplayed' => $isGridDisplayed,
                'isModRewriteActive' => $tools->isModRewriteActive(),
                'isShopContext' => $isShopContext,
                'isHtaccessFileValid' => $urlFileChecker->isHtaccessFileWritable(),
                'isRobotsTextFileValid' => $urlFileChecker->isRobotsFileWritable(),
                'isShopFeatureActive' => $isShopFeatureActive,
                'doesMainShopUrlExist' => $doesMainShopUrlExist,
                'enableSidebar' => true,
                'help_link' => $this->generateSidebarLink($request->attributes->get('_legacy_controller')),
                'helperDocLink' => $helperBlockLinkProvider->getLink('meta'),
                'indexPageId' => $metaDataProvider->getIdByPage('index'),
                'metaShowcaseCardName' => ShowcaseCard::SEO_URLS_CARD,
                'showcaseCardIsClosed' => $showcaseCardIsClosed,
            ]
        );
    }

    /**
     * Process the Meta configuration form.
     *
     * @param Request $request
     * @param FormHandlerInterface $formHandler
     * @param string $hookName
     *
     * @return FormInterface|RedirectResponse
     */
    protected function processForm(Request $request, FormHandlerInterface $formHandler, string $hookName)
    {
        $this->dispatchHook(
            'actionAdminShopParametersMetaControllerPostProcess' . $hookName . 'Before',
            ['controller' => $this]
        );

        $this->dispatchHook('actionAdminAdminShopParametersMetaControllerPostProcessBefore', ['controller' => $this]);

        $form = $formHandler->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $saveErrors = $formHandler->save($data);

            if (0 === count($saveErrors)) {
                $this->addFlash('success', $this->trans('Update successful', 'Admin.Notifications.Success'));

                return $this->redirectToRoute('admin_metas_index');
            } else {
                $this->flashErrors($saveErrors);
            }
        }

        return $form;
    }

    /**
     * Gets form builder.
     *
     * @return FormBuilderInterface
     */
    private function getMetaFormBuilder()
    {
        return $this->get('prestashop.core.form.builder.meta_form_builder');
    }

    /**
     * @return Handler\FormHandlerInterface
     */
    private function getMetaFormHandler()
    {
        return $this->get('prestashop.core.form.identifiable_object.meta_form_handler');
    }

    /**
     * Handles exception by its type and status code or by its type only and returns error message.
     *
     * @param Exception $exception
     *
     * @return string
     *
     * @todo use FrameworkAdminBundleController::getErrorMessageForException() instead
     */
    private function handleException(Exception $exception)
    {
        if (0 !== $exception->getCode()) {
            return $this->getExceptionByClassAndErrorCode($exception);
        }

        return $this->getExceptionByType($exception);
    }

    /**
     * Gets exception by class and error code.
     *
     * @param Exception $exception
     *
     * @return string
     */
    private function getExceptionByClassAndErrorCode(Exception $exception)
    {
        $exceptionDictionary = [
            MetaConstraintException::class => [
                MetaConstraintException::INVALID_URL_REWRITE => $this->trans(
                        'The %s field is not valid',
                        'Admin.Notifications.Error',
                        [
                            sprintf(
                                '"%s"',
                                $this->trans('Rewritten URL', 'Admin.Shopparameters.Feature')
                            ),
                        ]
                    ),
                MetaConstraintException::INVALID_PAGE_NAME => $this->trans(
                        'The %s field is required.',
                        'Admin.Notifications.Error',
                        [
                            sprintf(
                                '"%s"',
                                $this->trans('Page name', 'Admin.Shopparameters.Feature')
                            ),
                        ]
                    ),
                MetaConstraintException::INVALID_PAGE_TITLE => $this->trans(
                        'The %s field is not valid',
                        'Admin.Notifications.Error',
                        [
                            sprintf(
                                '"%s"',
                                $this->trans('Page title', 'Admin.Shopparameters.Feature')
                            ),
                        ]
                    ),
                MetaConstraintException::INVALID_META_DESCRIPTION => $this->trans(
                        'The %s field is not valid',
                        'Admin.Notifications.Error',
                        [
                            sprintf(
                                '"%s"',
                                $this->trans('Meta description', 'Admin.Global')
                            ),
                        ]
                    ),
                MetaConstraintException::INVALID_META_KEYWORDS => $this->trans(
                        'The %s field is not valid',
                        'Admin.Notifications.Error',
                        [
                            sprintf(
                                '"%s"',
                                $this->trans('Meta keywords', 'Admin.Global')
                            ),
                        ]
                    ),
            ],
        ];

        $exceptionClass = $exception::class;
        $exceptionCode = $exception->getCode();
        if (isset($exceptionDictionary[$exceptionClass][$exceptionCode])) {
            return $exceptionDictionary[$exceptionClass][$exceptionCode];
        }

        return $this->getFallbackErrorMessage($exceptionClass, $exceptionCode);
    }

    /**
     * Gets exception by class type.
     *
     * @param Exception $exception
     *
     * @return string
     */
    private function getExceptionByType(Exception $exception)
    {
        $exceptionDictionary = [
            MetaNotFoundException::class => $this->trans(
                'The object cannot be loaded (or found).',
                'Admin.Notifications.Error'
            ),
        ];

        $exceptionClass = $exception::class;
        if (isset($exceptionDictionary[$exceptionClass])) {
            return $exceptionDictionary[$exceptionClass];
        }

        return $this->getFallbackErrorMessage($exceptionClass, $exception->getCode());
    }

    /**
     * @return FormHandlerInterface
     */
    protected function getSetUpUrlsFormHandler(): FormHandlerInterface
    {
        return $this->get('prestashop.admin.meta_settings.set_up_urls.form_handler');
    }

    /**
     * @return FormHandlerInterface
     */
    protected function getShopUrlsFormHandler(): FormHandlerInterface
    {
        return $this->get('prestashop.admin.meta_settings.shop_urls.form_handler');
    }

    /**
     * @return FormHandlerInterface
     */
    protected function getUrlSchemaFormHandler(): FormHandlerInterface
    {
        return $this->get('prestashop.admin.meta_settings.url_schema.form_handler');
    }

    /**
     * @return FormHandlerInterface
     */
    protected function getSeoOptionsFormHandler(): FormHandlerInterface
    {
        return $this->get('prestashop.admin.meta_settings.seo_options.form_handler');
    }
}
