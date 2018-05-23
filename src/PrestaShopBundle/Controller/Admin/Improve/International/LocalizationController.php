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

namespace PrestaShopBundle\Controller\Admin\Improve\International;

use PrestaShop\PrestaShop\Core\Form\FormHandlerInterface;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class LocalizationController is responsible for handling "Improve > International > Localization" page
 */
class LocalizationController extends FrameworkBundleAdminController
{
    /**
     * Show localization settings page
     *
     * @Template("@PrestaShop/Admin/Improve/International/Localization/localization.html.twig")
     *
     * @param Request $request
     *
     * @return array
     */
    public function showSettingsAction(Request $request)
    {
        $legacyController = $request->attributes->get('_legacy_controller');

        $localizationForm = $this->getLocalizationFormHandler()->getForm();

        if (!extension_loaded('openssl')) {
            $this->addFlash('warning', $this->trans('Importing a new language may fail without the OpenSSL module. Please enable "openssl.so" on your server configuration.', 'Admin.International.Notification'));
        }

        return [
            'layoutHeaderToolbarBtn' => [],
            'layoutTitle' => $this->trans('Localization', 'Admin.Navigation.Menu'),
            'requireAddonsSearch' => true,
            'enableSidebar' => true,
            'help_link' => $this->generateSidebarLink($legacyController),
            'localizationForm' => $localizationForm->createView(),
        ];
    }

    /**
     * Save localization settings
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function saveSettingsAction(Request $request)
    {
        $localizationFormHandler = $this->getLocalizationFormHandler();

        $localizationForm = $localizationFormHandler->getForm();
        $localizationForm->handleRequest($request);

        if ($localizationForm->isSubmitted()) {
            $data = $localizationForm->getData();

            $errors = $localizationFormHandler->save($data);
            if (empty($errors)) {
                $this->addFlash('success', $this->trans('Update successful', 'Admin.Notifications.Success'));

                return $this->redirectToRoute('admin_international_localization_show_settings');
            }

            $this->flashErrors($errors);
        }

        return $this->redirectToRoute('admin_international_localization_show_settings');
    }

    /**
     * Returns localization settings from handler
     *
     * @return FormHandlerInterface
     */
    private function getLocalizationFormHandler()
    {
        return $this->get('prestashop.admin.localization.form_handler');
    }
}
