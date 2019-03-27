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
use PrestaShop\PrestaShop\Core\Form\FormHandlerInterface;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use PrestaShopBundle\Form\Admin\Improve\Design\MailTheme\GenerateMailsType;
use PrestaShopBundle\Security\Annotation\AdminSecurity;
use PrestaShopBundle\Service\MailTemplate\GenerateMailTemplatesService;
use Symfony\Component\Form\Form;
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
     * Show mail theme settings and generation page.
     *
     * @AdminSecurity("is_granted('read', request.get('_legacy_controller'))")
     *
     * @param Request $request
     *
     * @return Response
     */
    public function indexAction(Request $request)
    {
        $legacyController = $request->attributes->get('_legacy_controller');
        $generateThemeMailsForm = $this->createForm(GenerateMailsType::class);

        return $this->render('@PrestaShop/Admin/Improve/Design/MailTheme/index.html.twig', [
            'layoutHeaderToolbarBtn' => [],
            'layoutTitle' => $this->trans('Mail Theme', 'Admin.Navigation.Menu'),
            'requireAddonsSearch' => true,
            'enableSidebar' => true,
            'help_link' => $this->generateSidebarLink($legacyController),
            'mailThemeConfigurationForm' => $this->getMailThemeFormHandler()->getForm()->createView(),
            'generateMailsForm' => $generateThemeMailsForm->createView(),
        ]);
    }

    /**
     * Manage generation form post and generate mails.
     *
     * @AdminSecurity("is_granted(['create', 'update'], request.get('_legacy_controller'))")
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
            if (!$generateThemeMailsForm->isValid()) {
                $this->flashErrors($this->getFormErrorsForJS($generateThemeMailsForm));

                return $this->redirectToRoute('admin_mail_theme_index');
            }

            $data = $generateThemeMailsForm->getData();
            try {
                /** @var GenerateMailTemplatesService $generator */
                $generator = $this->get('prestashop.service.generate_mail_templates');
                //Overwrite theme folder if selected
                if (!empty($data['theme'])) {
                    $themeFolder = $this->getParameter('themes_dir') . $data['theme'];
                    $generator
                        ->setCoreMailsFolder($themeFolder . '/mails')
                        ->setModulesMailFolder($themeFolder . '/modules')
                    ;
                }

                $generator->generateMailTemplates($data['mail_theme'], $data['language'], $data['overwrite']);

                $flashMessage = 'Successfully generated mail templates for theme %s with locale %s';
                if ($data['overwrite']) {
                    $flashMessage = 'Successfully overrode mail templates for theme %s with locale %s';
                }
                $this->addFlash(
                    'success',
                    $this->trans(
                        sprintf(
                            $flashMessage,
                            $data['mail_theme'],
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
                            $data['mail_theme'],
                            $data['language']
                        ),
                        'Admin.Notifications.Error'
                    ),
                    $e->getMessage(),
                ]);
            }
        }

        return $this->redirectToRoute('admin_mail_theme_index');
    }

    /**
     * Save mail theme configuration
     *
     * @AdminSecurity("is_granted(['update'], request.get('_legacy_controller'))")
     *
     * @param Request $request
     *
     * @return Response
     */
    public function saveConfigurationAction(Request $request)
    {
        /** @var FormHandlerInterface $formHandler */
        $formHandler = $this->getMailThemeFormHandler();
        /** @var Form $form */
        $form = $formHandler->getForm()->handleRequest($request);

        if ($form->isSubmitted()) {
            if (!$form->isValid()) {
                $this->flashErrors($this->getFormErrorsForJS($form));

                return $this->redirectToRoute('admin_mail_theme_index');
            }

            $errors = $formHandler->save($form->getData());
            if (empty($errors)) {
                $this->addFlash(
                    'success',
                    $this->trans(
                        'Successfully saved mail theme configuration.',
                        'Admin.Notifications.Success'
                    )
                );
            } else {
                $this->flashErrors($errors);
            }
        }

        return $this->redirectToRoute('admin_mail_theme_index');
    }

    /**
     * @return FormHandlerInterface
     */
    private function getMailThemeFormHandler()
    {
        return $this->get('prestashop.admin.mail_theme.form_handler');
    }
}
