<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace Tests\Integration\Classes\Mail;

use Configuration;
use Context;
use Language;
use Mail;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Process\Process;

/**
 * Verifies that Mail::send() resolves the mail-themes URL in a real SMTP message.
 *
 * Uses a short-lived SMTP listener on the loopback interface, so this test does
 * not depend on MailDev or another service being available in CI.
 */
class MailThemesUrlIntegrationTest extends TestCase
{
    public function testMailThemesUrlIsResolvedInSentMessage(): void
    {
        $context = Context::getContext();
        $shopId = (int) $context->shop->id;
        self::assertGreaterThan(0, $shopId);

        $langId = (int) Configuration::get('PS_LANG_DEFAULT', null, null, $shopId);
        self::assertNotEmpty(Language::getIsoById($langId));

        // Mail::send() always falls back to English when resolving mail templates.
        $templateDir = _PS_ROOT_DIR_ . '/mails/en/';
        self::assertDirectoryExists($templateDir);
        $template = 'pr43114_mail_themes_' . bin2hex(random_bytes(8));
        $html = $templateDir . $template . '.html';
        $txt = $templateDir . $template . '.txt';

        $tmpDir = sys_get_temp_dir() . '/ps-mail-url-' . bin2hex(random_bytes(8));
        self::assertTrue(mkdir($tmpDir, 0700));
        $portFile = $tmpDir . '/port';
        $messageFile = $tmpDir . '/message';

        $server = new Process([
            PHP_BINARY,
            __DIR__ . '/Fixtures/capture_smtp.php',
            $portFile,
            $messageFile,
        ]);
        $server->setTimeout(25);

        $original = [];
        $settings = [
            'PS_MAIL_METHOD' => Mail::METHOD_SMTP,
            'PS_MAIL_SERVER' => '127.0.0.1',
            'PS_MAIL_SMTP_ENCRYPTION' => 'off',
            'PS_MAIL_USER' => '',
            'PS_MAIL_PASSWD' => '',
            'PS_MAIL_TYPE' => Mail::TYPE_HTML,
            'PS_MAIL_DKIM_ENABLE' => 0,
            'PS_LOG_EMAILS' => 0,
            'PS_SHOP_EMAIL' => 'sender@example.test',
        ];

        try {
            self::assertNotFalse(file_put_contents(
                $html,
                '<html><body><img src="{mail_themes_url}/modern/assets/example.png"></body></html>'
            ));
            self::assertNotFalse(file_put_contents($txt, 'Test email'));

            $server->start();
            $deadline = microtime(true) + 10;
            while (!is_file($portFile) && $server->isRunning() && microtime(true) < $deadline) {
                usleep(20000);
            }
            self::assertFileExists($portFile, $server->getErrorOutput());
            $port = (int) file_get_contents($portFile);
            self::assertGreaterThan(0, $port);

            $settings['PS_MAIL_SMTP_PORT'] = $port;
            foreach ($settings as $key => $value) {
                $original[$key] = Configuration::get($key, null, null, $shopId);
                self::assertTrue(
                    Configuration::updateValue($key, $value, false, null, $shopId),
                    sprintf('Could not set test configuration %s', $key)
                );
            }

            self::assertTrue(Mail::send(
                $langId,
                $template,
                'Mail themes URL regression test',
                [],
                'receiver@example.test',
                null,
                'sender@example.test',
                'Test',
                null,
                null,
                _PS_MAIL_DIR_,
                false,
                $shopId
            ));

            $deadline = microtime(true) + 5;
            while (!is_file($messageFile) && microtime(true) < $deadline) {
                usleep(20000);
            }
            self::assertFileExists($messageFile, $server->getErrorOutput());

            $raw = file_get_contents($messageFile);
            self::assertIsString($raw);
            $decodedMessage = quoted_printable_decode($raw);
            self::assertStringNotContainsString('{mail_themes_url}', $decodedMessage);

            $expectedUrl = rtrim($context->link->getBaseLink($shopId), '/') . '/mails/themes';
            self::assertStringContainsString(
                $expectedUrl . '/modern/assets/example.png',
                $decodedMessage
            );
        } finally {
            if ($server->isRunning()) {
                $server->stop(1);
            }

            foreach ($original as $key => $value) {
                Configuration::updateValue($key, $value, false, null, $shopId);
            }

            @unlink($html);
            @unlink($txt);
            @unlink($portFile);
            @unlink($messageFile);
            @rmdir($tmpDir);
        }
    }
}
