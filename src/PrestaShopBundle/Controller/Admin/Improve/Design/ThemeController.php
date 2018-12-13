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
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController as AbstractAdminController;
use PrestaShopBundle\Security\Voter\PageVoter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class ThemeController manages "Improve > Design > Theme & Logo" pages.
 */
class ThemeController extends AbstractAdminController
{
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

            return $this->redirectToRoute('admin_theme_customize_page_layouts');
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
            $this->isGranted(PageVoter::UPDATE, $request->attributes->get('_legacy_controller'))
        ;
    }
}
