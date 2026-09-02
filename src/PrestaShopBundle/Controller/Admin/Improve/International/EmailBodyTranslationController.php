<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\Controller\Admin\Improve\International;

use Exception;
use PrestaShop\PrestaShop\Core\Domain\MailTemplate\Command\EditEmailBodyTemplateCommand;
use PrestaShop\PrestaShop\Core\Domain\MailTemplate\Exception\EmailTemplateConstraintException;
use PrestaShop\PrestaShop\Core\Domain\MailTemplate\Exception\EmailTemplateException;
use PrestaShop\PrestaShop\Core\Domain\MailTemplate\Exception\EmailTemplateNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\MailTemplate\Query\GetEmailBodyTemplateForEditing;
use PrestaShop\PrestaShop\Core\Domain\MailTemplate\QueryResult\EditableEmailBodyTemplate;
use PrestaShop\PrestaShop\Core\Domain\MailTemplate\ValueObject\EmailTemplateSource;
use PrestaShop\PrestaShop\Core\Grid\Definition\Factory\EmailBodyTemplateDefinitionFactory;
use PrestaShop\PrestaShop\Core\Grid\GridFactoryInterface;
use PrestaShop\PrestaShop\Core\Search\Filters\EmailBodyTemplateFilters;
use PrestaShopBundle\Controller\Admin\PrestaShopAdminController;
use PrestaShopBundle\Form\Admin\Improve\Design\MailTheme\EmailBodyTemplateEditType;
use PrestaShopBundle\Security\Attribute\AdminSecurity;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class EmailBodyTranslationController extends PrestaShopAdminController
{
    #[AdminSecurity("is_granted('read', request.get('_legacy_controller'))")]
    public function indexAction(
        Request $request,
        string $locale,
        #[Autowire(service: 'prestashop.core.grid.factory.email_body_template')]
        GridFactoryInterface $gridFactory,
        EmailBodyTemplateFilters $filters,
    ): Response {
        $currentFilters = $filters->getFilters() ?? [];
        $currentFilters['locale'] = $locale;
        $filters->set('filters', $currentFilters);

        $grid = $gridFactory->getGrid($filters);

        return $this->render('@PrestaShop/Admin/Improve/International/EmailBodyTranslation/index.html.twig', [
            'enableSidebar' => true,
            'help_link' => $this->generateSidebarLink($request->attributes->get('_legacy_controller')),
            'layoutTitle' => $this->trans('Email body templates — %locale%', ['%locale%' => $locale], 'Admin.Navigation.Menu'),
            'emailBodyTemplateGrid' => $this->presentGrid($grid),
            'locale' => $locale,
        ]);
    }

    #[AdminSecurity("is_granted('update', request.get('_legacy_controller'))", redirectRoute: 'admin_international_translations_show_settings')]
    public function editAction(
        Request $request,
        string $locale,
        string $source,
        string $templateName,
    ): Response {
        $sourceObject = $this->parseSource($source);

        try {
            /** @var EditableEmailBodyTemplate $editableTemplate */
            $editableTemplate = $this->dispatchQuery(new GetEmailBodyTemplateForEditing(
                $templateName,
                $locale,
                $sourceObject->getSource(),
                $sourceObject->getModuleName(),
            ));
        } catch (EmailTemplateNotFoundException $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages()));

            return $this->redirectToRoute('admin_email_body_translation_index', ['locale' => $locale]);
        }

        $form = $this->createForm(EmailBodyTemplateEditType::class, [
            'html_content' => $editableTemplate->getHtmlContent(),
            'txt_content' => $editableTemplate->getTxtContent(),
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            try {
                $this->dispatchCommand(new EditEmailBodyTemplateCommand(
                    $templateName,
                    $locale,
                    $sourceObject->getSource(),
                    $sourceObject->getModuleName(),
                    $data['html_content'] ?? '',
                    $data['txt_content'] ?? '',
                ));

                $this->addFlash(
                    'success',
                    $this->trans('Email template "%template%" has been successfully updated.', ['%template%' => $templateName], 'Admin.International.Notification')
                );

                return $this->redirectToRoute('admin_email_body_translation_index', ['locale' => $locale]);
            } catch (Exception $e) {
                $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages()));
            }
        }

        return $this->render('@PrestaShop/Admin/Improve/International/EmailBodyTranslation/edit.html.twig', [
            'enableSidebar' => true,
            'help_link' => $this->generateSidebarLink($request->attributes->get('_legacy_controller')),
            'layoutTitle' => $this->trans(
                'Edit email template: %template% (%locale%)',
                ['%template%' => $templateName, '%locale%' => $locale],
                'Admin.Navigation.Menu'
            ),
            'emailBodyTemplateEditForm' => $form->createView(),
            'templateName' => $templateName,
            'locale' => $locale,
            'source' => $source,
        ]);
    }

    #[AdminSecurity("is_granted('read', request.get('_legacy_controller'))")]
    public function searchAction(
        Request $request,
        string $locale,
        #[Autowire(service: 'prestashop.core.grid.definition.factory.email_body_template')]
        EmailBodyTemplateDefinitionFactory $definitionFactory,
    ): RedirectResponse {
        return $this->buildSearchResponse(
            $definitionFactory,
            $request,
            EmailBodyTemplateDefinitionFactory::GRID_ID,
            'admin_email_body_translation_index',
            ['locale'],
        );
    }

    private function parseSource(string $source): EmailTemplateSource
    {
        if (str_contains($source, ':')) {
            [$type, $moduleName] = explode(':', $source, 2);

            return new EmailTemplateSource($type, $moduleName);
        }

        return new EmailTemplateSource($source);
    }

    /**
     * @return array<string, string>
     */
    private function getErrorMessages(): array
    {
        return [
            EmailTemplateNotFoundException::class => $this->trans(
                'The email template was not found.',
                [],
                'Admin.International.Notification'
            ),
            EmailTemplateConstraintException::class => $this->trans(
                'The email template content is invalid. JavaScript is not allowed in HTML email templates.',
                [],
                'Admin.International.Notification'
            ),
            EmailTemplateException::class => $this->trans(
                'An error occurred while processing the email template.',
                [],
                'Admin.International.Notification'
            ),
        ];
    }
}
