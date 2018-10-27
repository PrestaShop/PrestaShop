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

namespace PrestaShopBundle\Controller\Admin\Improve\Design;

use PrestaShop\PrestaShop\Core\Domain\Meta\DataTransferObject\LayoutCustomizationPage;
use PrestaShop\PrestaShop\Core\Domain\Meta\Query\GetPagesForLayoutCustomization;
use PrestaShop\PrestaShop\Core\Domain\Shop\Command\UploadLogosCommand;
use PrestaShop\PrestaShop\Core\Domain\Shop\Exception\NotSupportedFaviconExtensionException;
use PrestaShop\PrestaShop\Core\Domain\Shop\Exception\ShopException;
use PrestaShop\PrestaShop\Core\Domain\Theme\Command\EnableThemeCommand;
use PrestaShop\PrestaShop\Core\Domain\Theme\Command\ImportThemeCommand;
use PrestaShop\PrestaShop\Core\Domain\Theme\Exception\ThemeException;
use PrestaShop\PrestaShop\Core\Domain\Theme\ValueObject\ThemeImportSource;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController as AbstractAdminController;
use PrestaShopBundle\Form\Admin\Improve\Design\Theme\ImportThemeType;
use PrestaShopBundle\Form\Admin\Improve\Design\Theme\ShopLogosType;
use PrestaShopBundle\Security\Annotation\AdminSecurity;
use PrestaShopBundle\Security\Annotation\DemoRestricted;
use PrestaShopBundle\Security\Voter\PageVoter;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class ThemeController manages "Improve > Design > Theme & Logo" pages.
 */
class ThemeController extends AbstractAdminController
{
    /**
     * Show main themes page.
     *
     * @return Response
     */
    public function indexAction()
    {
        $themeProvider = $this->get('prestashop.adapter.addons.theme.theme_provider');
        $logoProvider = $this->get('prestashop.core.shop.logo.logo_provider');

        return $this->render('@PrestaShop/Admin/Improve/Design/Theme/index.html.twig', [
            'baseShopUrl' => $this->get('prestashop.adapter.shop.url.base_url_provider')->getUrl(),
            'shopLogosForm' => $this->getLogosUploadForm()->createView(),
            'logoProvider' => $logoProvider,
            'installedTheme' => $themeProvider->getInstalledTheme(),
            'notInstalledThemes' => $themeProvider->getNotInstalledThemes(),
            'isDevModeOn' => $this->get('prestashop.adapter.legacy.configuration')->get('_PS_MODE_DEV_'),
            'isSingleShopContext' => $this->get('prestashop.adapter.shop.context')->isSingleShopContext(),
        ]);
    }

    /**
     * Upload shop logos.
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function uploadLogosAction(Request $request)
    {
        $logosUploadForm = $this->getLogosUploadForm();
        $logosUploadForm->handleRequest($request);

        if ($logosUploadForm->isSubmitted()) {
            $data = $logosUploadForm->getData();

            try {
                $command = new UploadLogosCommand();

                if ($data['header_logo']) {
                    $command->setUploadedHeaderLogo($data['header_logo']);
                }

                if ($data['mail_logo']) {
                    $command->setUploadedMailLogo($data['mail_logo']);
                }

                if ($data['invoice_logo']) {
                    $command->setUploadedInvoiceLogo($data['invoice_logo']);
                }

                if ($data['favicon']) {
                    $command->setUploadedFavicon($data['favicon']);
                }

                $this->getCommandBus()->handle($command);
            } catch (ShopException $e) {
                $this->addFlash('error', $this->handleUploadLogosException($e));
            }
        }

        return $this->redirectToRoute('admin_themes_index');
    }

    /**
     * Export current theme.
     *
     * @AdminSecurity(
     *     "is_granted('create', request.get('_legacy_controller'))",
     *     redirectRoute="admin_themes_index",
     *     message="You do not have permission to view this."
     * )
     *
     * @return RedirectResponse
     */
    public function exportAction()
    {
        $themeProvider = $this->get('prestashop.adapter.addons.theme.theme_provider');
        $exporter = $this->get('prestashop.core.addon.theme.exporter');

        $path = $exporter->export($themeProvider->getInstalledTheme());

        $this->addFlash(
            'success',
            $this->trans(
                'Your theme has been correctly exported: %path%',
                'Admin.Notifications.Success',
                ['%path%' => $path]
            )
        );

        return $this->redirectToRoute('admin_themes_index');
    }

    /**
     * Import new theme.
     *
     * @AdminSecurity(
     *     "is_granted('create', request.get('_legacy_controller'))",
     *     redirectRoute="admin_themes_index",
     *     message="You do not have permission to add this."
     * )
     * @DemoRestricted(redirectRoute="admin_themes_index")
     *
     * @param Request $request
     *
     * @return Response
     */
    public function importAction(Request $request)
    {
        $importThemeForm = $this->createForm(ImportThemeType::class);
        $importThemeForm->handleRequest($request);

        if ($importThemeForm->isSubmitted()) {
            $data = $importThemeForm->getData();

            try {
                if ($data['import_from_computer']) {
                    $importSource = ThemeImportSource::fromArchive($data['import_from_computer']);
                } elseif ($data['import_from_web']) {
                    $importSource = ThemeImportSource::fromWeb($data['import_from_web']);
                } elseif ($data['import_from_ftp']) {
                    $importSource = ThemeImportSource::fromFtp($data['import_from_ftp']);
                }

                $this->getCommandBus()->handle(new ImportThemeCommand($importSource));

                return $this->redirectToRoute('admin_themes_index');
            } catch (ThemeException $e) {
                throw $e; //@todo: handle properly
            }
        }

        return $this->render('@PrestaShop/Admin/Improve/Design/Theme/import.html.twig', [
            'importThemeForm' => $importThemeForm->createView(),
        ]);
    }

    /**
     * Enable theme.
     *
     * @param string $themeName
     *
     * @return RedirectResponse
     */
    public function enableAction($themeName)
    {
        try {
            $this->getCommandBus()->handle(new EnableThemeCommand($themeName));
        } catch (ThemeException $e) {

        }

        return $this->redirectToRoute('admin_themes_index');
    }

    /**
     * Show Front Office theme's pages layout customization.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function customizePageLayoutsAction(Request $request)
    {
        $canCustomizeLayout = $this->canCustomizePageLayouts($request);

        if (!$canCustomizeLayout) {
            $this->addFlash(
                'error',
                $this->trans('You do not have permission to edit this.', 'Admin.Notifications.Error')
            );
        }

        /** @var LayoutCustomizationPage[] $pages */
        $pages = $this->getQueryBus()->handle(new GetPagesForLayoutCustomization());

        $pageLayoutCustomizationFormFactory =
            $this->get('prestashop.bundle.form.admin.improve.design.theme.page_layout_customization_form_factory');
        $pageLayoutCustomizationForm = $pageLayoutCustomizationFormFactory->create($pages);
        $pageLayoutCustomizationForm->handleRequest($request);

        if ($canCustomizeLayout && $pageLayoutCustomizationForm->isSubmitted()) {
            if ($this->isDemoModeEnabled()) {
                $this->addFlash('error', $this->getDemoModeErrorMessage());

                return $this->redirectToRoute('admin_theme_customize_page_layouts');
            }

            $themePageLayoutsCustomizer = $this->get('prestashop.core.addon.theme.theme.page_layouts_customizer');
            $themePageLayoutsCustomizer->customize($pageLayoutCustomizationForm->getData()['layouts']);

            $this->addFlash('success', $this->trans('Successful update.', 'Admin.Notifications.Success'));

            return $this->redirectToRoute('admin_themes_index');
        }

        return $this->render('@PrestaShop/Admin/Improve/Design/Theme/customize_page_layouts.html.twig', [
            'pageLayoutCustomizationForm' => $pageLayoutCustomizationForm->createView(),
            'pages' => $pages,
        ]);
    }

    /**
     * @param Request $request
     *
     * @return bool
     */
    protected function canCustomizePageLayouts(Request $request)
    {
        return !$this->isDemoModeEnabled() &&
            $this->isGranted(PageVoter::UPDATE, $request->attributes->get('_legacy_controller'));
    }

    /**
     * @return FormInterface
     */
    protected function getLogosUploadForm()
    {
        return $this->createForm(ShopLogosType::class);
    }

    /**
     * Handles exception that was thrown when uploading shop logos.
     *
     * @param ShopException $e
     *
     * @return string Error message for exception.
     */
    private function handleUploadLogosException(ShopException $e)
    {
        $type = get_class($e);

        $errorMessages = [
            NotSupportedFaviconExtensionException::class =>
                $this->trans('Image format not recognized, allowed formats are: .ico', 'Admin.Notifications.Error'),
        ];

        if (isset($errorMessages[$type])) {
            return $errorMessages[$type];
        }

        return $this->getFallbackErrorMessage($type, $e->getCode());
    }
}
