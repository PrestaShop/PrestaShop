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

namespace PrestaShopBundle\Controller\Admin\Improve\Design;

use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use Symfony\Component\HttpFoundation\Request;

/**
 * Displays themes from Addons under "Improve > Design > Themes Catalog"
 *
 * @author Michael Käfer <michael.kaefer1@gmx.at>
 */
class ThemeController extends FrameworkBundleAdminController
{
    /**
     * Displays themes from Addons under "Improve > Design > Themes Catalog"
     *
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request)
    {
        $context = $this->getContext();
        $configuration = $this->get('prestashop.adapter.legacy.configuration');

        $pageContent = file_get_contents('https://addons.prestashop.com/iframe/search-1.7.php'
            . http_build_query([
                'psVersion' => $configuration->get('_PS_VERSION_'),
                'isoLang' => $context->language->iso_code,
                'isoCurrency' => $context->currency->iso_code,
                'isoCountry' => $this->getContext()->country->iso_code,
                'activity' => $configuration->getInt('PS_SHOP_ACTIVITY'),
                'parentUrl' => $request->getSchemeAndHttpHost(),
                'onlyThemes' => 1
            ])
        );

        return $this->render('@PrestaShop/Admin/Improve/Design/ThemesCatalogPage/addons_store.html.twig', [
            'pageContent' => $pageContent,
            'layoutHeaderToolbarBtn' => [],
            'layoutTitle' => $this->trans('Themes Catalog', 'Admin.Navigation.Menu'),
            'requireAddonsSearch' => true,
            'requireBulkActions' => false,
            'showContentHeader' => true,
            'enableSidebar' => true,
            'help_link' => $this->generateSidebarLink('AdminThemesCatalog'),
            'requireFilterStatus' => false,
        ]);
    }
}
