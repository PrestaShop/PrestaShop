<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\PrestaShopBundle\Twig;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Bridge\Twig\Extension\TranslationExtension;
use Symfony\Component\Translation\Loader\ArrayLoader;
use Symfony\Component\Translation\Translator;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use Twig\TwigFunction;

class TwoFactorCodeFormTest extends TestCase
{
    #[DataProvider('formContexts')]
    public function testSharedFormUsesLoginTranslationsAndPreservesItsAction(string $route, bool $trustedOption): void
    {
        $translator = new Translator('en');
        $translator->addLoader('array', new ArrayLoader());
        $translator->addResource('array', [
            'Authentication code' => 'Translated authentication code',
            'Authenticator app' => 'Translated authenticator app',
            'Verify' => 'Translated verify',
        ], 'en', 'Admin.Login.Feature');

        $loader = new FilesystemLoader();
        $loader->addPath(dirname(__DIR__, 4) . '/src/PrestaShopBundle/Resources/views', 'PrestaShop');
        $twig = new Environment($loader);
        $twig->addExtension(new TranslationExtension($translator));
        $twig->addFunction(new TwigFunction('path', static fn (string $name): string => '/' . $name));
        $twig->addFunction(new TwigFunction('csrf_token', static fn (string $id): string => 'csrf-' . $id));

        $html = $twig->render('@PrestaShop/Admin/TwoFactorAuthentication/Blocks/two_factor_code_form.html.twig', [
            'checkPathUrl' => null,
            'checkPathRoute' => $route,
            'availableTwoFactorProviders' => ['email', 'totp'],
            'twoFactorProvider' => 'totp',
            'authCodeParameterName' => '_auth_code',
            'displayTrustedOption' => $trustedOption,
            'trustedParameterName' => '_trusted',
            'isCsrfProtectionEnabled' => true,
            'csrfParameterName' => '_token',
            'csrfTokenId' => 'two-factor',
            'providerSwitchMethod' => $trustedOption ? 'get' : 'post',
            'providerSwitchRoute' => $trustedOption ? '2fa_login' : 'admin_employees_prepare_regenerate_backup_codes',
            'providerSwitchCsrfParameter' => '_token_prepare_backup_codes',
            'providerSwitchCsrfTokenId' => 'prepare-backup-codes',
        ]);

        self::assertStringContainsString('action="/' . $route . '"', $html);
        self::assertStringContainsString('Translated authentication code', $html);
        self::assertStringContainsString('Translated authenticator app', $html);
        self::assertStringContainsString('Translated verify', $html);
        self::assertStringContainsString('name="_token" value="csrf-two-factor"', $html);
        self::assertSame($trustedOption, str_contains($html, 'name="_trusted"'));
        self::assertSame(!$trustedOption, str_contains($html, 'name="_token_prepare_backup_codes" value="csrf-prepare-backup-codes"'));
        self::assertSame($trustedOption ? 1 : 3, substr_count($html, '<form method="post"'));
    }

    public static function formContexts(): iterable
    {
        yield 'login' => ['2fa_login_check', true];
        yield 'backup code regeneration' => ['admin_employees_regenerate_backup_codes', false];
    }
}
