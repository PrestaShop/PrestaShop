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
use PrestaShop\PrestaShop\Adapter\File\RobotsTextFileGenerator;
use PrestaShop\PrestaShop\Adapter\Meta\MetaEraser;
use PrestaShop\PrestaShop\Adapter\Routes\DefaultRouteProvider;
use PrestaShop\PrestaShop\Adapter\Shop\ShopUrlDataProvider;
use PrestaShop\PrestaShop\Adapter\Tools;
use PrestaShop\PrestaShop\Core\Domain\Meta\Exception\MetaConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Meta\Exception\MetaNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\ShowcaseCard\Query\GetShowcaseCardIsClosed;
use PrestaShop\PrestaShop\Core\Domain\ShowcaseCard\ValueObject\ShowcaseCard;
use PrestaShop\PrestaShop\Core\Form\FormHandlerInterface;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\Builder\FormBuilderInterface;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\Handler\FormHandlerInterface as IdentifiableFormHandlerInterface;
use PrestaShop\PrestaShop\Core\Grid\GridFactoryInterface;
use PrestaShop\PrestaShop\Core\Meta\MetaDataProviderInterface;
use PrestaShop\PrestaShop\Core\Search\Filters\MetaFilters;
use PrestaShop\PrestaShop\Core\Util\HelperCard\DocumentationLinkProviderInterface;
use PrestaShop\PrestaShop\Core\Util\Url\UrlFileCheckerInterface;
use PrestaShopBundle\Controller\Admin\PrestaShopAdminController;
use PrestaShopBundle\Security\Attribute\AdminSecurity;
use PrestaShopBundle\Security\Attribute\DemoRestricted;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class MetaController is responsible for page display and all actions used in Configure -> Shop parameters ->
 * Traffic & Seo -> Seo & Urls tab.
 */
class MetaController extends PrestaShopAdminController
{
    /**
     * All these services implement the same interface and are based on the same parent class, so we can't
     * rely on autowiring not add them into the getSubscribedServices that expects a type as the values.
     *
     * So we have to inject them in the constructor to use the Autowire attribute and define the specific
     * service name, this way they are usable in all the shared protected/public methods in this controller.
     *
     * @param FormHandlerInterface $setUpUrlsFormHandler
     * @param FormHandlerInterface $shopUrlsFormHandler
     * @param FormHandlerInterface $seoOptionsFormHandler
     * @param FormHandlerInterface $urlSchemaFormHandler
     */
    public function __construct(
        #[Autowire(service: 'prestashop.admin.meta_settings.set_up_urls.form_handler')]
        protected FormHandlerInterface $setUpUrlsFormHandler,
        #[Autowire(service: 'prestashop.admin.meta_settings.shop_urls.form_handler')]
        protected FormHandlerInterface $shopUrlsFormHandler,
        #[Autowire(service: 'prestashop.admin.meta_settings.seo_options.form_handler')]
        protected FormHandlerInterface $seoOptionsFormHandler,
        #[Autowire(service: 'prestashop.admin.meta_settings.url_schema.form_handler')]
        protected FormHandlerInterface $urlSchemaFormHandler,
        #[Autowire(service: 'prestashop.core.grid.factory.meta')]
        protected GridFactoryInterface $metaGridFactory,
    ) {
    }

    /**
     * This controller uses a lot of services that are used in many methods, especially they are all shared via the
     * renderForm method. Injecting all those services and passing them by parameters would complicate the code a lot,
     * so instead, we register them, so they can be fetched more easily via the container and our getter methods.
     *
     * @return string[]
     */
    public static function getSubscribedServices(): array
    {
        return parent::getSubscribedServices() + [
            DefaultRouteProvider::class => DefaultRouteProvider::class,
            ShopUrlDataProvider::class => ShopUrlDataProvider::class,
            MetaDataProviderInterface::class => MetaDataProviderInterface::class,
            UrlFileCheckerInterface::class => UrlFileCheckerInterface::class,
            DocumentationLinkProviderInterface::class => DocumentationLinkProviderInterface::class,
            Tools::class => Tools::class,
        ];
    }

    #[AdminSecurity("is_granted('read', request.get('_legacy_controller'))")]
    public function indexAction(MetaFilters $filters, Request $request): Response
    {
        $setUpUrlsForm = $this->setUpUrlsFormHandler->getForm();
        $shopUrlsForm = $this->shopUrlsFormHandler->getForm();
        $seoOptionsForm = $this->seoOptionsFormHandler->getForm();
        $isRewriteSettingEnabled = (bool) $this->getConfiguration()->get('PS_REWRITING_SETTINGS');

        $urlSchemaForm = null;
        if ($isRewriteSettingEnabled) {
            $urlSchemaForm = $this->urlSchemaFormHandler->getForm();
        }

        return $this->doRenderForm($request, $filters, $setUpUrlsForm, $shopUrlsForm, $seoOptionsForm, $urlSchemaForm);
    }

    #[AdminSecurity("is_granted('create', request.get('_legacy_controller'))", message: 'You do not have permission to add this.')]
    public function createAction(
        Request $request,
        #[Autowire(service: 'prestashop.core.form.builder.meta_form_builder')]
        FormBuilderInterface $metaFormBuilder,
        #[Autowire(service: 'prestashop.core.form.identifiable_object.meta_form_handler')]
        IdentifiableFormHandlerInterface $metaFormHandler,
    ): Response {
        $data = [];
        $metaForm = $metaFormBuilder->getForm($data);
        $metaForm->handleRequest($request);

        try {
            $result = $metaFormHandler->handle($metaForm);

            if (null !== $result->getIdentifiableObjectId()) {
                $this->addFlash('success', $this->trans('Successful creation', [], 'Admin.Notifications.Success'));

                return $this->redirectToRoute('admin_metas_index');
            }
        } catch (Exception $exception) {
            $this->addFlash('error', $this->getErrorMessageForException($exception, $this->getErrorMessages()));
        }

        return $this->render('@PrestaShop/Admin/Configure/ShopParameters/TrafficSeo/Meta/create.html.twig', [
            'meta_form' => $metaForm->createView(),
            'multistoreInfoTip' => $this->trans(
                'Note that this feature is only available in the "all stores" context. It will be added to all your stores.',
                [],
                'Admin.Notifications.Info'
            ),
            'multistoreIsUsed' => $this->getShopContext()->isMultiShopUsed(),
            'layoutTitle' => $this->trans('New page configuration', [], 'Admin.Navigation.Menu'),
        ]);
    }

    #[AdminSecurity("is_granted('update', request.get('_legacy_controller'))", message: 'You do not have permission to edit this.', redirectRoute: 'admin_metas_index')]
    public function editAction(
        int $metaId,
        Request $request,
        #[Autowire(service: 'prestashop.core.form.builder.meta_form_builder')]
        FormBuilderInterface $metaFormBuilder,
        #[Autowire(service: 'prestashop.core.form.identifiable_object.meta_form_handler')]
        IdentifiableFormHandlerInterface $metaFormHandler,
    ): Response {
        try {
            $metaForm = $metaFormBuilder->getFormFor($metaId);
            $metaForm->handleRequest($request);

            $result = $metaFormHandler->handleFor($metaId, $metaForm);

            if ($result->isSubmitted() && $result->isValid()) {
                $this->addFlash('success', $this->trans('Successful update', [], 'Admin.Notifications.Success'));

                return $this->redirectToRoute('admin_metas_index');
            }
        } catch (Exception $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages()));

            return $this->redirectToRoute('admin_metas_index');
        }

        return $this->render('@PrestaShop/Admin/Configure/ShopParameters/TrafficSeo/Meta/edit.html.twig', [
            'meta_form' => $metaForm->createView(),
            'layoutTitle' => $this->trans(
                'Editing configuration for %name%',
                [
                    '%name%' => $metaForm->getData()['page_name'],
                ],
                'Admin.Navigation.Menu',
            ),
        ]);
    }

    #[DemoRestricted(redirectRoute: 'admin_metas_index')]
    #[AdminSecurity("is_granted('delete', request.get('_legacy_controller'))", message: 'You do not have permission to delete this.')]
    public function deleteAction(
        int $metaId,
        MetaEraser $metaEraser,
    ): RedirectResponse {
        $errors = $metaEraser->erase([$metaId]);

        if (!empty($errors)) {
            $this->addFlashErrors($errors);
        } else {
            $this->addFlash(
                'success',
                $this->trans('Successful deletion', [], 'Admin.Notifications.Success')
            );
        }

        return $this->redirectToRoute('admin_metas_index');
    }

    #[DemoRestricted(redirectRoute: 'admin_metas_index')]
    #[AdminSecurity("is_granted('delete', request.get('_legacy_controller'))", message: 'You do not have permission to delete this.')]
    public function deleteBulkAction(
        Request $request,
        MetaEraser $metaEraser,
    ): RedirectResponse {
        $metaToDelete = $request->request->all('meta_bulk');
        $errors = $metaEraser->erase($metaToDelete);

        if (!empty($errors)) {
            $this->addFlashErrors($errors);
        } else {
            $this->addFlash(
                'success',
                $this->trans('The selection has been successfully deleted.', [], 'Admin.Notifications.Success')
            );
        }

        return $this->redirectToRoute('admin_metas_index');
    }

    #[DemoRestricted(redirectRoute: 'admin_metas_index')]
    #[AdminSecurity("is_granted('update', request.get('_legacy_controller'))", message: 'You do not have permission to edit this.', redirectRoute: 'admin_metas_index')]
    public function processSetUpUrlsFormAction(MetaFilters $filters, Request $request): Response|RedirectResponse
    {
        $formProcessResult = $this->processForm(
            $request,
            $this->setUpUrlsFormHandler,
            'SetUpUrls'
        );

        if ($formProcessResult instanceof RedirectResponse) {
            return $formProcessResult;
        }

        $shopUrlsForm = $this->shopUrlsFormHandler->getForm();
        $seoOptionsForm = $this->seoOptionsFormHandler->getForm();

        $isRewriteSettingEnabled = (bool) $this->getConfiguration()->get('PS_REWRITING_SETTINGS');
        $urlSchemaForm = null;
        if ($isRewriteSettingEnabled) {
            $urlSchemaForm = $this->urlSchemaFormHandler->getForm();
        }

        return $this->doRenderForm($request, $filters, $formProcessResult, $shopUrlsForm, $seoOptionsForm, $urlSchemaForm);
    }

    #[DemoRestricted(redirectRoute: 'admin_metas_index')]
    #[AdminSecurity("is_granted('update', request.get('_legacy_controller'))", message: 'You do not have permission to edit this.', redirectRoute: 'admin_metas_index')]
    public function processShopUrlsFormAction(MetaFilters $filters, Request $request): Response|RedirectResponse
    {
        $formProcessResult = $this->processForm(
            $request,
            $this->shopUrlsFormHandler,
            'ShopUrls'
        );

        if ($formProcessResult instanceof RedirectResponse) {
            return $formProcessResult;
        }

        $setUpUrlsForm = $this->setUpUrlsFormHandler->getForm();
        $seoOptionsForm = $this->seoOptionsFormHandler->getForm();
        $isRewriteSettingEnabled = (bool) $this->getConfiguration()->get('PS_REWRITING_SETTINGS');

        $urlSchemaForm = null;
        if ($isRewriteSettingEnabled) {
            $urlSchemaForm = $this->urlSchemaFormHandler->getForm();
        }

        return $this->doRenderForm($request, $filters, $setUpUrlsForm, $formProcessResult, $seoOptionsForm, $urlSchemaForm);
    }

    #[DemoRestricted(redirectRoute: 'admin_metas_index')]
    #[AdminSecurity("is_granted('update', request.get('_legacy_controller'))", message: 'You do not have permission to edit this.', redirectRoute: 'admin_metas_index')]
    public function processUrlSchemaFormAction(MetaFilters $filters, Request $request): Response|RedirectResponse
    {
        $formProcessResult = $this->processForm(
            $request,
            $this->urlSchemaFormHandler,
            'UrlSchema'
        );

        if ($formProcessResult instanceof RedirectResponse) {
            return $formProcessResult;
        }

        $setUpUrlsForm = $this->setUpUrlsFormHandler->getForm();
        $shopUrlsForm = $this->shopUrlsFormHandler->getForm();
        $seoOptionsForm = $this->seoOptionsFormHandler->getForm();

        return $this->doRenderForm($request, $filters, $setUpUrlsForm, $shopUrlsForm, $seoOptionsForm, $formProcessResult);
    }

    #[DemoRestricted(redirectRoute: 'admin_metas_index')]
    #[AdminSecurity("is_granted('update', request.get('_legacy_controller'))", message: 'You do not have permission to edit this.', redirectRoute: 'admin_metas_index')]
    public function processSeoOptionsFormAction(MetaFilters $filters, Request $request): Response|RedirectResponse
    {
        $formProcessResult = $this->processForm(
            $request,
            $this->seoOptionsFormHandler,
            'SeoOptions'
        );

        if ($formProcessResult instanceof RedirectResponse) {
            return $formProcessResult;
        }

        $setUpUrlsForm = $this->setUpUrlsFormHandler->getForm();
        $shopUrlsForm = $this->shopUrlsFormHandler->getForm();
        $isRewriteSettingEnabled = (bool) $this->getConfiguration()->get('PS_REWRITING_SETTINGS');

        $urlSchemaForm = null;
        if ($isRewriteSettingEnabled) {
            $urlSchemaForm = $this->urlSchemaFormHandler->getForm();
        }

        return $this->doRenderForm($request, $filters, $setUpUrlsForm, $shopUrlsForm, $formProcessResult, $urlSchemaForm);
    }

    #[DemoRestricted(redirectRoute: 'admin_metas_index')]
    #[AdminSecurity("is_granted('create', request.get('_legacy_controller')) && is_granted('update', request.get('_legacy_controller')) && is_granted('delete', request.get('_legacy_controller'))", redirectRoute: 'admin_metas_index')]
    public function generateRobotsFileAction(RobotsTextFileGenerator $robotsTextFileGenerator): RedirectResponse
    {
        $rootDir = $this->getConfiguration()->get('_PS_ROOT_DIR_');

        if (!$robotsTextFileGenerator->generateFile()) {
            $this->addFlash(
                'error',
                $this->trans(
                    'Cannot write into file: %filename%. Please check write permissions.',
                    [
                        '%filename%' => $rootDir . '/robots.txt',
                    ],
                    'Admin.Notifications.Error',
                )
            );

            return $this->redirectToRoute('admin_metas_index');
        }

        $this->addFlash(
            'success',
            $this->trans('Successful update', [], 'Admin.Notifications.Success')
        );

        return $this->redirectToRoute('admin_metas_index');
    }

    private function doRenderForm(
        Request $request,
        MetaFilters $filters,
        FormInterface $setUpUrlsForm,
        FormInterface $shopUrlsForm,
        FormInterface $seoOptionsForm,
        ?FormInterface $urlSchemaForm = null
    ): Response {
        $isShopContext = $this->getShopContext()->getShopConstraint()->isSingleShopContext();
        $isShopFeatureActive = $this->getShopContext()->isMultiShopEnabled();

        $isGridDisplayed = !($isShopFeatureActive && !$isShopContext);
        $presentedGrid = null;
        if ($isGridDisplayed) {
            $grid = $this->metaGridFactory->getGrid($filters);
            $presentedGrid = $this->presentGrid($grid);
        }

        /** @var Tools $tools */
        $tools = $this->container->get(Tools::class);
        /** @var UrlFileCheckerInterface $urlFileChecker */
        $urlFileChecker = $this->container->get(UrlFileCheckerInterface::class);
        /** @var DefaultRouteProvider $defaultRoutesProvider */
        $defaultRoutesProvider = $this->container->get(DefaultRouteProvider::class);
        /** @var DocumentationLinkProviderInterface $helperBlockLinkProvider */
        $helperBlockLinkProvider = $this->container->get(DocumentationLinkProviderInterface::class);
        /** @var MetaDataProviderInterface $metaDataProvider */
        $metaDataProvider = $this->container->get(MetaDataProviderInterface::class);

        $showcaseCardIsClosed = $this->dispatchQuery(
            new GetShowcaseCardIsClosed($this->getEmployeeContext()->getEmployee()->getId(), ShowcaseCard::SEO_URLS_CARD)
        );

        $doesMainShopUrlExist = $this->container->get(ShopUrlDataProvider::class)->doesMainShopUrlExist();

        return $this->render(
            '@PrestaShop/Admin/Configure/ShopParameters/TrafficSeo/Meta/index.html.twig',
            [
                'layoutHeaderToolbarBtn' => [
                    'add' => [
                        'href' => $this->generateUrl('admin_metas_create'),
                        'desc' => $this->trans('Set up a new page', [], 'Admin.Shopparameters.Feature'),
                        'icon' => 'add_circle_outline',
                    ],
                ],
                'grid' => $presentedGrid,
                'setUpUrlsForm' => $setUpUrlsForm->createView(),
                'shopUrlsForm' => $shopUrlsForm->createView(),
                'urlSchemaForm' => $urlSchemaForm?->createView(),
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

    protected function processForm(Request $request, FormHandlerInterface $formHandler, string $hookName): FormInterface|RedirectResponse
    {
        $this->dispatchHookWithParameters(
            'actionAdminShopParametersMetaControllerPostProcess' . $hookName . 'Before',
            ['controller' => $this]
        );

        $this->dispatchHookWithParameters('actionAdminAdminShopParametersMetaControllerPostProcessBefore', ['controller' => $this]);

        $form = $formHandler->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $saveErrors = $formHandler->save($data);

            if (0 === count($saveErrors)) {
                $this->addFlash('success', $this->trans('Update successful', [], 'Admin.Notifications.Success'));

                return $this->redirectToRoute('admin_metas_index');
            } else {
                $this->addFlashErrors($saveErrors);
            }
        }

        return $form;
    }

    private function getErrorMessages(): array
    {
        return [
            MetaConstraintException::class => [
                MetaConstraintException::INVALID_URL_REWRITE => $this->trans(
                    'The %s field is not valid',
                    [
                        sprintf(
                            '"%s"',
                            $this->trans('Rewritten URL', [], 'Admin.Shopparameters.Feature')
                        ),
                    ],
                    'Admin.Notifications.Error',
                ),
                MetaConstraintException::INVALID_PAGE_NAME => $this->trans(
                    'The %s field is required.',
                    [
                        sprintf(
                            '"%s"',
                            $this->trans('Page name', [], 'Admin.Shopparameters.Feature')
                        ),
                    ],
                    'Admin.Notifications.Error',
                ),
                MetaConstraintException::INVALID_PAGE_TITLE => $this->trans(
                    'The %s field is not valid',
                    [
                        sprintf(
                            '"%s"',
                            $this->trans('Page title', [], 'Admin.Shopparameters.Feature')
                        ),
                    ],
                    'Admin.Notifications.Error',
                ),
                MetaConstraintException::INVALID_META_DESCRIPTION => $this->trans(
                    'The %s field is not valid',
                    [
                        sprintf(
                            '"%s"',
                            $this->trans('Meta description', [], 'Admin.Global')
                        ),
                    ],
                    'Admin.Notifications.Error',
                ),
            ],
            MetaNotFoundException::class => $this->trans(
                'The object cannot be loaded (or found).',
                [],
                'Admin.Notifications.Error'
            ),
        ];
    }
}
