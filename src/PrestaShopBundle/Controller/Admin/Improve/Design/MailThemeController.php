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

namespace PrestaShopBundle\Controller\Admin\Improve\Design;

use Mail;
use PrestaShop\PrestaShop\Adapter\MailTemplate\MailPreviewVariablesBuilder;
use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Domain\MailTemplate\Command\GenerateThemeMailTemplatesCommand;
use PrestaShop\PrestaShop\Core\Employee\ContextEmployeeProviderInterface;
use PrestaShop\PrestaShop\Core\Exception\CoreException;
use PrestaShop\PrestaShop\Core\Exception\FileNotFoundException;
use PrestaShop\PrestaShop\Core\Exception\InvalidArgumentException;
use PrestaShop\PrestaShop\Core\Form\FormHandlerInterface;
use PrestaShop\PrestaShop\Core\Language\LanguageRepositoryInterface;
use PrestaShop\PrestaShop\Core\MailTemplate\Layout\LayoutInterface;
use PrestaShop\PrestaShop\Core\MailTemplate\MailTemplateInterface;
use PrestaShop\PrestaShop\Core\MailTemplate\MailTemplateRendererInterface;
use PrestaShop\PrestaShop\Core\MailTemplate\ThemeCatalogInterface;
use PrestaShop\PrestaShop\Core\MailTemplate\ThemeInterface;
use PrestaShop\PrestaShop\Core\MailTemplate\Transformation\MailVariablesTransformation;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use PrestaShopBundle\Form\Admin\Improve\Design\MailTheme\GenerateMailsType;
use PrestaShopBundle\Form\Admin\Improve\Design\MailTheme\TranslateMailsBodyType;
use PrestaShopBundle\Security\Annotation\AdminSecurity;
use PrestaShopBundle\Service\TranslationService;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

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
        $translateMailsBodyForm = $this->createForm(TranslateMailsBodyType::class);
        /** @var ThemeCatalogInterface $themeCatalog */
        $themeCatalog = $this->get('prestashop.core.mail_template.theme_catalog');
        $mailThemes = $themeCatalog->listThemes();

        return $this->render('@PrestaShop/Admin/Improve/Design/MailTheme/index.html.twig', [
            'layoutHeaderToolbarBtn' => [],
            'layoutTitle' => $this->trans('Email Theme', 'Admin.Navigation.Menu'),
            'enableSidebar' => true,
            'help_link' => $this->generateSidebarLink($legacyController),
            'mailThemeConfigurationForm' => $this->getMailThemeFormHandler()->getForm()->createView(),
            'generateMailsForm' => $generateThemeMailsForm->createView(),
            'translateMailsBodyForm' => $translateMailsBodyForm->createView(),
            'mailThemes' => $mailThemes,
        ]);
    }

    /**
     * Manage generation form post and generate mails.
     *
     * @AdminSecurity("is_granted('create', request.get('_legacy_controller'))")
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
                $coreMailsFolder = '';
                $modulesMailFolder = '';
                //Overwrite theme folder if selected
                if (!empty($data['theme'])) {
                    $themeFolder = $this->getParameter('themes_dir') . '/' . $data['theme'];
                    if (is_dir($themeFolder . '/mails')) {
                        $coreMailsFolder = $themeFolder . '/mails';
                    }
                    if (is_dir($themeFolder . '/modules')) {
                        $modulesMailFolder = $themeFolder . '/modules';
                    }
                }

                $generateCommand = new GenerateThemeMailTemplatesCommand(
                    $data['mailTheme'],
                    $data['language'],
                    $data['overwrite'],
                    $coreMailsFolder,
                    $modulesMailFolder
                );

                /** @var CommandBusInterface $commandBus */
                $commandBus = $this->get('prestashop.core.command_bus');
                $commandBus->handle($generateCommand);

                if ($data['overwrite']) {
                    $this->addFlash(
                        'success',
                        $this->trans(
                            'Successfully overwrote email templates for theme %s with locale %s',
                            'Admin.Notifications.Success',
                            [
                                $data['mailTheme'],
                                $data['language'],
                            ]
                        )
                    );
                } else {
                    $this->addFlash(
                        'success',
                        $this->trans(
                            'Successfully generated email templates for theme %s with locale %s',
                            'Admin.Notifications.Success',
                            [
                                $data['mailTheme'],
                                $data['language'],
                            ]
                        )
                    );
                }
            } catch (CoreException $e) {
                $this->flashErrors([
                    $this->trans(
                        sprintf(
                            'Cannot generate email templates for theme %s with locale %s',
                            $data['mailTheme'],
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
     * @AdminSecurity("is_granted('update', request.get('_legacy_controller'))")
     *
     * @param Request $request
     *
     * @return Response
     *
     * @throws \Exception
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
                        'Email theme configuration saved successfully',
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
     * Preview the list of layouts for a defined theme
     *
     * @AdminSecurity("is_granted('read', request.get('_legacy_controller'))")
     *
     * @param Request $request
     * @param string $theme
     *
     * @return Response
     *
     * @throws InvalidArgumentException
     */
    public function previewThemeAction(Request $request, $theme)
    {
        $legacyController = $request->attributes->get('_legacy_controller');

        /** @var ThemeCatalogInterface $themeCatalog */
        $themeCatalog = $this->get('prestashop.core.mail_template.theme_catalog');
        /** @var ThemeInterface $mailTheme */
        $mailTheme = $themeCatalog->getByName($theme);

        return $this->render('@PrestaShop/Admin/Improve/Design/MailTheme/preview.html.twig', [
            'layoutHeaderToolbarBtn' => [],
            'layoutTitle' => $this->trans('Preview Theme %s', 'Admin.Design.Feature', [$mailTheme->getName()]),
            'enableSidebar' => true,
            'help_link' => $this->generateSidebarLink($legacyController),
            'mailTheme' => $mailTheme,
        ]);
    }

    /**
     * This action allows to send a test mail of a specific email template, however the Mail
     * class used to send emails is not modular enough to allow sending templates on the fly.
     * This would require either:
     *  - a little modification of the Mail class to add an easy way to send a template content (rather than its name)
     *  - a full refacto of the Mail class which wouldn't be coupled to static files any more
     *
     * These modifications will be performed in a future release so for now we can only send test emails
     * with the current email theme using generated static files.
     *
     * @AdminSecurity("is_granted('read', request.get('_legacy_controller'))")
     *
     * @param string $theme
     * @param string $layout
     * @param string $locale
     * @param string $module
     *
     * @return Response
     *
     * @throws InvalidArgumentException
     */
    public function sendTestMailAction($theme, $layout, $locale, $module = '')
    {
        if ($this->configuration->get('PS_MAIL_THEME') !== $theme) {
            $this->addFlash(
                'error',
                $this->trans(
                    'Cannot send test email for theme %theme% because it is not your current theme',
                    'Admin.Notifications.Error',
                    [
                        '%theme%' => $theme,
                    ]
                )
            );

            return $this->redirectToRoute('admin_mail_theme_preview', ['theme' => $theme]);
        }

        /** @var ContextEmployeeProviderInterface $employeeProvider */
        $employeeProvider = $this->get('prestashop.adapter.data_provider.employee');
        $employeeData = $employeeProvider->getData();

        /** @var LanguageRepositoryInterface $languageRepository */
        $languageRepository = $this->get('prestashop.core.admin.lang.repository');
        $language = $languageRepository->getOneByLocaleOrIsoCode($locale);
        if (null === $language) {
            throw new InvalidArgumentException(sprintf('Cannot find Language with locale or isoCode %s', $locale));
        }

        if (empty($module)) {
            $templatePath = _PS_MAIL_DIR_;
        } else {
            $templatePath = _PS_MODULE_DIR_ . $module . '/mails/';
        }

        /** @var MailPreviewVariablesBuilder $variablesBuilder */
        $variablesBuilder = $this->get('prestashop.adapter.mail_template.preview_variables_builder');
        $mailLayout = $this->getMailLayout($theme, $layout, $module);
        $mailVariables = $variablesBuilder->buildTemplateVariables($mailLayout);

        $mailSent = Mail::send(
            $language->getId(),
            $layout,
            $this->trans('Test email %template%', 'Admin.Design.Feature', ['%template%' => $layout]),
            $mailVariables,
            $employeeData['email'],
            $employeeData['firstname'] . ' ' . $employeeData['lastname'],
            $employeeData['email'],
            $employeeData['firstname'] . ' ' . $employeeData['lastname'],
            null,
            null,
            $templatePath
        );

        if ($mailSent) {
            $this->addFlash(
                'success',
                $this->trans(
                    'Test email for layout %layout% was successfully sent to %email%',
                    'Admin.Notifications.Success',
                    [
                        '%layout%' => $layout,
                        '%email%' => $employeeData['email'],
                    ]
                )
            );
        } else {
            $this->addFlash(
                'error',
                $this->trans(
                    'Cannot send test email for layout %layout%',
                    'Admin.Notifications.Error',
                    [
                        '%layout%' => $layout,
                    ]
                )
            );
        }

        return $this->redirectToRoute('admin_mail_theme_preview', ['theme' => $theme]);
    }

    /**
     * @AdminSecurity(
     *     "is_granted('update', request.get('_legacy_controller'))",
     *     message="You do not have permission to update this."
     * )
     *
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function translateBodyAction(Request $request)
    {
        $translateMailsBodyForm = $this->createForm(TranslateMailsBodyType::class);
        $translateMailsBodyForm->handleRequest($request);

        if (!$translateMailsBodyForm->isSubmitted() || !$translateMailsBodyForm->isValid()) {
            $this->addFlash(
                'error',
                $this->trans(
                    'Cannot translate emails body content',
                    'Admin.Notifications.Error'
                )
            );

            return $this->redirectToRoute('admin_mail_theme_index');
        }

        $translateData = $translateMailsBodyForm->getData();
        $language = $translateData['language'];
        /** @var TranslationService $translationService */
        $translationService = $this->get('prestashop.service.translation');
        $locale = $translationService->langToLocale($language);

        return $this->redirectToRoute('admin_international_translation_overview', [
            'lang' => $language,
            'locale' => $locale,
            'type' => 'mails_body',
        ]);
    }

    /**
     * Preview a mail layout from a defined theme
     *
     * @AdminSecurity("is_granted('read', request.get('_legacy_controller'))")
     *
     * @param string $theme
     * @param string $layout
     * @param string $type
     * @param string $locale
     * @param string $module
     *
     * @return Response
     *
     * @throws FileNotFoundException
     * @throws InvalidArgumentException
     */
    public function previewLayoutAction($theme, $layout, $type, $locale, $module = '')
    {
        $renderedLayout = $this->renderLayout($theme, $layout, $type, $locale, $module);

        return new Response($renderedLayout);
    }

    /**
     * Display the raw source of a theme layout (mainly useful for developers/integrators)
     *
     * @AdminSecurity("is_granted('read', request.get('_legacy_controller'))")
     *
     * @param string $theme
     * @param string $layout
     * @param string $type
     * @param string $locale
     * @param string $module
     *
     * @return Response
     *
     * @throws FileNotFoundException
     * @throws InvalidArgumentException
     */
    public function rawLayoutAction($theme, $layout, $type, $locale, $module = '')
    {
        $renderedLayout = $this->renderLayout($theme, $layout, $type, $locale, $module);

        $response = new Response($renderedLayout, Response::HTTP_OK, [
            'Content-Type' => 'text/plain',
        ]);

        return $response;
    }

    /**
     * Dynamically display an email template, this is usually used by the MailGenerator but this action
     * allows to display preview before generating the file (handy when you are developing an email theme)
     *
     * @param string $themeName
     * @param string $layoutName
     * @param string $type
     * @param string $locale
     * @param string $module
     *
     * @return string
     *
     * @throws FileNotFoundException
     * @throws InvalidArgumentException
     */
    private function renderLayout($themeName, $layoutName, $type, $locale = '', $module = '')
    {
        $layout = $this->getMailLayout($themeName, $layoutName, $module);

        /** @var LanguageRepositoryInterface $languageRepository */
        $languageRepository = $this->get('prestashop.core.admin.lang.repository');
        if (empty($locale)) {
            $locale = $this->getContext()->language->locale;
        }
        $language = $languageRepository->getOneByLocaleOrIsoCode($locale);
        if (null === $language) {
            throw new InvalidArgumentException(sprintf('Cannot find Language with locale or isoCode %s', $locale));
        }

        /** @var MailPreviewVariablesBuilder $variablesBuilder */
        $variablesBuilder = $this->get('prestashop.adapter.mail_template.preview_variables_builder');
        $mailLayoutVariables = $variablesBuilder->buildTemplateVariables($layout);

        /** @var MailTemplateRendererInterface $renderer */
        $renderer = $this->get('prestashop.core.mail_template.mail_template_renderer');
        //Special case for preview, we fill the mail variables
        $renderer->addTransformation(new MailVariablesTransformation(MailTemplateInterface::HTML_TYPE, $mailLayoutVariables));
        $renderer->addTransformation(new MailVariablesTransformation(MailTemplateInterface::TXT_TYPE, $mailLayoutVariables));

        switch ($type) {
            case MailTemplateInterface::HTML_TYPE:
                $renderedLayout = $renderer->renderHtml($layout, $language);
                break;
            case MailTemplateInterface::TXT_TYPE:
                $renderedLayout = $renderer->renderTxt($layout, $language);
                break;
            default:
                throw new NotFoundHttpException(sprintf('Requested type %s is not managed, please use one of these: %s', $type, implode(',', [MailTemplateInterface::HTML_TYPE, MailTemplateInterface::TXT_TYPE])));
        }

        return $renderedLayout;
    }

    /**
     * @param string $themeName
     * @param string $layoutName
     * @param string $module
     *
     * @return LayoutInterface
     *
     * @throws FileNotFoundException
     * @throws InvalidArgumentException
     */
    private function getMailLayout($themeName, $layoutName, $module)
    {
        /** @var ThemeCatalogInterface $themeCatalog */
        $themeCatalog = $this->get('prestashop.core.mail_template.theme_catalog');
        /** @var ThemeInterface $theme */
        $theme = $themeCatalog->getByName($themeName);

        /** @var LayoutInterface $layout */
        $layout = null;
        /* @var LayoutInterface $layoutInterface */
        foreach ($theme->getLayouts() as $layoutInterface) {
            if ($layoutInterface->getName() == $layoutName
                && $layoutInterface->getModuleName() == $module
            ) {
                $layout = $layoutInterface;
                break;
            }
        }

        if (null === $layout) {
            throw new FileNotFoundException(sprintf('Cannot find layout %s%s in theme %s', empty($module) ? '' : $module . ':', $layoutName, $themeName));
        }

        return $layout;
    }

    /**
     * @return FormHandlerInterface
     */
    private function getMailThemeFormHandler(): FormHandlerInterface
    {
        return $this->get('prestashop.admin.mail_theme.form_handler');
    }
}
