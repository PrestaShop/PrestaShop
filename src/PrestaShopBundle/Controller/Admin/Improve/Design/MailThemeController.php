<?php
/**
 * 2007-2019 PrestaShop SA and Contributors
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
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShopBundle\Controller\Admin\Improve\Design;

use PrestaShop\PrestaShop\Core\Exception\CoreException;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use PrestaShopBundle\Form\Admin\Improve\Design\MailTheme\GenerateMailsType;
use PrestaShopBundle\Security\Annotation\AdminSecurity;
use PrestaShopBundle\Service\MailTemplate\GenerateMailTemplatesService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class MailThemeController manages mail theme generation, you can define the shop
 * mail theme, and regenerate mail in a specific language.
 *
 * Accessible via "Design > Mail Theme"
 */
class MailThemeController extends FrameworkBundleAdminController
{
    /**
     * Show localization settings page.
     *
     * @AdminSecurity("is_granted('read', request.get('_legacy_controller'))", message="Access denied.")
     *
     * @param Request $request
     *
     * @return Response
     */
    public function indexAction(Request $request)
    {
        $legacyController = $request->attributes->get('_legacy_controller');
        $defaultMailTheme = $this->configuration->get('PS_MAIL_THEME');
        $generateThemeMailsForm = $this->createForm(GenerateMailsType::class, ['theme' => $defaultMailTheme]);

        return $this->render('@PrestaShop/Admin/Improve/Design/MailTheme/generate_mails_form.html.twig', [
            'layoutHeaderToolbarBtn' => [],
            'layoutTitle' => $this->trans('Mail Theme', 'Admin.Navigation.Menu'),
            'requireAddonsSearch' => true,
            'enableSidebar' => true,
            'help_link' => $this->generateSidebarLink($legacyController),
            'generateMailsForm' => $generateThemeMailsForm->createView(),
        ]);
    }

    /**
     * Show localization settings page.
     *
     * @AdminSecurity("is_granted(['create', 'update'], request.get('_legacy_controller'))", message="Access denied.")
     *
     * @param Request $request
     *
     * @return Response
     */
    public function generateMailsAction(Request $request)
    {
        $generateThemeMailsForm = $this->createForm(GenerateMailsType::class);
        $generateThemeMailsForm->handleRequest($request);

        if ($generateThemeMailsForm->isSubmitted()) {
            $data = $generateThemeMailsForm->getData();

            try {
                /** @var GenerateMailTemplatesService $generator */
                $generator = $this->get('prestashop.service.generate_mail_templates');
                //Overwrite theme folder if selected
                if (!empty($data['theme'])) {
                    $themeFolder = $this->getParameter('kernel.project_dir') . '/themes/' . $data['theme'];
                    $generator
                        ->setCoreMailsFolder($themeFolder . '/mails')
                        ->setModulesMailFolder($themeFolder . '/modules')
                    ;
                }

                $generator->generateMailTemplates($data['mailTheme'], $data['language'], $data['overwrite']);

                $flashMessage = 'Successfully generated mail templates for theme %s with locale %s';
                if ($data['overwrite']) {
                    $flashMessage = 'Successfully overrode mail templates for theme %s with locale %s';
                }
                $this->addFlash(
                    'success',
                    $this->trans(
                        sprintf(
                            $flashMessage,
                            $data['mailTheme'],
                            $data['language']
                        ),
                        'Admin.Notifications.Success'
                    )
                );
            } catch (CoreException $e) {
                $this->flashErrors([
                    $this->trans(
                        sprintf(
                            'Could not generate mail templates for theme %s with locale %s',
                            $data['mailTheme'],
                            $data['language']
                        ),
                        'Admin.Notifications.Error'
                    ),
                    $e->getMessage(),
                ]);
            }
        }

        return $this->redirectToRoute('admin_mail_theme_generate_form');
    }
}
