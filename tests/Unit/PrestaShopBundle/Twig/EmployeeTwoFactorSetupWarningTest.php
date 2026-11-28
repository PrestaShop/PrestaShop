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
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;
use Symfony\Component\Translation\Translator;
use Twig\Environment;
use Twig\Loader\ArrayLoader;
use Twig\Loader\ChainLoader;
use Twig\Loader\FilesystemLoader;
use Twig\TwigFunction;

class EmployeeTwoFactorSetupWarningTest extends TestCase
{
    #[DataProvider('setupStates')]
    public function testWarningOnlyAppearsWhenSetupIsAvailableAndRequired(bool $featureEnabled, bool $totpEnabled, bool $expected): void
    {
        $files = new FilesystemLoader();
        $files->addPath(dirname(__DIR__, 4) . '/src/PrestaShopBundle/Resources/views', 'PrestaShop');
        $twig = new Environment(new ChainLoader([
            new ArrayLoader([
                '@PrestaShop/Admin/layout.html.twig' => '{% block content %}{% endblock %}',
                '@PrestaShop/Admin/Configure/AdvancedParameters/Employee/Blocks/form.html.twig' => '',
            ]),
            $files,
        ]));
        $twig->addExtension(new TranslationExtension(new Translator('en')));
        $twig->addFunction(new TwigFunction('asset', static fn (string $path): string => $path));
        $form = ['vars' => ['errors' => []]];
        if ($featureEnabled) {
            $form['two_factor_enabled'] = [];
        }

        $html = $twig->render('@PrestaShop/Admin/Configure/AdvancedParameters/Employee/edit.html.twig', [
            'app' => ['session' => new Session(new MockArraySessionStorage())],
            'employeeForm' => $form,
            'isRestrictedAccess' => true,
            'editableEmployee' => [
                'isTwoFactorRequired' => true,
                'twoFactorEnabled' => $totpEnabled,
                'twoFactorTotpEnabled' => $totpEnabled,
                'twoFactorEmailEnabled' => false,
            ],
        ]);

        self::assertSame($expected, str_contains($html, 'Two-factor authentication is required for your account.'));
    }

    public static function setupStates(): iterable
    {
        yield 'globally disabled' => [false, false, false];
        yield 'setup required' => [true, false, true];
        yield 'TOTP configured' => [true, true, false];
    }
}
