<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\PrestaShopBundle\Twig;

use DOMDocument;
use DOMXPath;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Bridge\Twig\Extension\FormExtension;
use Symfony\Bridge\Twig\Extension\TranslationExtension;
use Symfony\Component\Form\FormRenderer;
use Symfony\Component\Form\FormView;
use Symfony\Component\Translation\Translator;
use Twig\Environment;
use Twig\Loader\ArrayLoader;
use Twig\Loader\ChainLoader;
use Twig\Loader\FilesystemLoader;
use Twig\RuntimeLoader\FactoryRuntimeLoader;
use Twig\TwigFunction;

class EmployeeTwoFactorFormsTest extends TestCase
{
    #[DataProvider('backupCodeStates')]
    public function testIndependentPostsExcludeEmployeeDataAndProvideANoScriptSetup(bool $hasCodes, string $operation, string $route, string $token): void
    {
        $loader = new FilesystemLoader();
        $loader->addPath(dirname(__DIR__, 4) . '/src/PrestaShopBundle/Resources/views', 'PrestaShop');
        $twig = new Environment(new ChainLoader([new ArrayLoader([
            '@PrestaShop/Admin/macros.html.twig' => '',
            '@PrestaShop/Admin/Helpers/password_feedback.html.twig' => '',
        ]), $loader]));
        $twig->addExtension(new FormExtension());
        $twig->addExtension(new TranslationExtension(new Translator('en')));
        $twig->addFunction(new TwigFunction('path', static fn (string $name): string => '/' . $name));
        $twig->addFunction(new TwigFunction('csrf_token', static fn (string $id): string => 'csrf-' . $id));
        $renderer = $this->createMock(FormRenderer::class);
        $renderer->method('renderBlock')->willReturnCallback(static fn (FormView $view, string $block): string => match ($block) {
            'form_start' => '<form id="employee-main"><input name="employee[password]" value="sensitive">',
            'form_end' => '</form>',
            default => '',
        });
        $renderer->method('searchAndRenderBlock')->willReturn('');
        $twig->addRuntimeLoader(new FactoryRuntimeLoader([FormRenderer::class => static fn (): FormRenderer => $renderer]));
        $form = new FormView();
        $form->children['two_factor_totp_enabled'] = new FormView($form);
        $form->children['two_factor_tot_verification_code'] = new FormView($form);

        $html = $twig->render('@PrestaShop/Admin/Configure/AdvancedParameters/Employee/Blocks/form.html.twig', [
            'employeeForm' => $form,
            'isRestrictedAccess' => true,
            'canManageBackupCodes' => true,
            'hasBackupCodes' => $hasCodes,
            'canResetTwoFactor' => false,
            'editableEmployeeId' => 42,
        ]);
        $document = new DOMDocument();
        $document->loadHTML($html);
        $xpath = new DOMXPath($document);
        $backupForm = '//form[@id="employee-' . $operation . '-backup-codes-form"]';
        self::assertSame('post', $xpath->evaluate('string(' . $backupForm . '/@method)'));
        self::assertSame('/' . $route, $xpath->evaluate('string(' . $backupForm . '/@action)'));
        self::assertSame(1, $xpath->query($backupForm . '//input')->length);
        self::assertSame('csrf-' . $token . '_42', $xpath->evaluate('string(' . $backupForm . '//input[@name="_token_' . $token . '"]/@value)'));
        self::assertSame(1, $xpath->query('//form[@id="employee-main"]//button[@type="submit" and @form="employee-' . $operation . '-backup-codes-form"]')->length);
        self::assertSame(0, $xpath->query('//form[@id="employee-main"]//input[starts-with(@name,"_token_")]')->length);
        self::assertSame(0, $xpath->query('//form//form')->length);
        $setupForm = '//form[@id="employee-initialize-totp-form"]';
        self::assertSame('post', $xpath->evaluate('string(' . $setupForm . '/@method)'));
        self::assertSame('/admin_employees_initialize_totp', $xpath->evaluate('string(' . $setupForm . '/@action)'));
        self::assertSame(1, $xpath->query($setupForm . '//input')->length);
        self::assertSame('csrf-initialize_totp_42', $xpath->evaluate('string(' . $setupForm . '//input[@name="_token_initialize_totp"]/@value)'));
        self::assertSame(1, $xpath->query('//noscript//button[@type="submit" and @form="employee-initialize-totp-form"]')->length);
    }

    public function testEmailOnlyConfirmationHasADedicatedCsrfProtectedResendWithoutJavascript(): void
    {
        $loader = new FilesystemLoader();
        $loader->addPath(dirname(__DIR__, 4) . '/src/PrestaShopBundle/Resources/views', 'PrestaShop');
        $twig = new Environment(new ChainLoader([new ArrayLoader([
            '@PrestaShop/Admin/layout.html.twig' => '{% block content %}{% endblock %}',
        ]), $loader]));
        $twig->addExtension(new TranslationExtension(new Translator('en')));
        $twig->addFunction(new TwigFunction('path', static fn (string $name): string => '/' . $name));
        $twig->addFunction(new TwigFunction('csrf_token', static fn (string $id): string => 'csrf-' . $id));
        $html = $twig->render('@PrestaShop/Admin/Configure/AdvancedParameters/Employee/confirm_regenerate_backup_codes.html.twig', [
            'employeeId' => 42,
            'authenticationError' => null,
            'emailCodePreparationRequired' => false,
            'providerSwitchRoute' => 'admin_employees_prepare_regenerate_backup_codes',
            'providerSwitchRouteParams' => ['employeeId' => 42, 'sendCode' => 1],
            'providerSwitchMethod' => 'post',
            'providerSwitchCsrfParameter' => '_token_prepare_backup_codes',
            'providerSwitchCsrfTokenId' => 'prepare_backup_codes_42',
            'twoFactorProvider' => 'email',
            'availableTwoFactorProviders' => ['email'],
            'checkPathRoute' => 'admin_employees_regenerate_backup_codes',
            'checkPathUrl' => null,
            'authCodeParameterName' => '_auth_code',
            'displayTrustedOption' => false,
            'isCsrfProtectionEnabled' => true,
            'csrfParameterName' => '_token',
            'csrfTokenId' => 'regenerate_backup_codes_42',
            'submitLabel' => 'Regenerate backup codes',
            'logoutPath' => null,
        ]);
        self::assertStringContainsString('Resend code', $html);
        self::assertStringContainsString('method="post" action="/admin_employees_prepare_regenerate_backup_codes"', $html);
        self::assertStringContainsString('name="_token_prepare_backup_codes" value="csrf-prepare_backup_codes_42"', $html);
        self::assertSame(2, substr_count($html, '<form method="post"'));
    }

    public static function backupCodeStates(): iterable
    {
        yield 'first generation' => [false, 'generate', 'admin_employees_generate_backup_codes', 'generate_backup_codes'];
        yield 'regeneration confirmation' => [true, 'regenerate', 'admin_employees_prepare_regenerate_backup_codes', 'prepare_backup_codes'];
    }
}
