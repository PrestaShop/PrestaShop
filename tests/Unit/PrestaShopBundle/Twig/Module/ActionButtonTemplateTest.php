<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\PrestaShopBundle\Twig\Module;

use PHPUnit\Framework\TestCase;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

/**
 * The update button carries the message its module attached to the update confirmation.
 * The attribute has to stay out of the markup when the module supplied nothing, otherwise
 * the front end cannot tell "no message" from "empty message".
 */
class ActionButtonTemplateTest extends TestCase
{
    private const TEMPLATE = 'action_button.html.twig';

    private Environment $twig;

    protected function setUp(): void
    {
        $this->twig = new Environment(new FilesystemLoader(
            dirname(__DIR__, 5) . '/src/PrestaShopBundle/Resources/views/Admin/Module/Includes'
        ));
    }

    public function testTheUpdateButtonCarriesTheModuleMessage(): void
    {
        $markup = $this->render('upgrade', 'Your settings will be reset.');

        $this->assertStringContainsString(
            'data-confirm-message="Your settings will be reset."',
            $markup
        );
    }

    public function testTheAttributeIsAbsentWhenTheModuleSuppliedNoMessage(): void
    {
        $this->assertStringNotContainsString('data-confirm-message', $this->render('upgrade', ''));
    }

    public function testTheAttributeIsAbsentWhenTheParameterIsNotPassedAtAll(): void
    {
        $markup = $this->twig->render(self::TEMPLATE, [
            'action' => 'upgrade',
            'name' => 'dummy_payment',
            'url' => '/upgrade',
            'label' => 'Update',
            'classes' => 'btn',
        ]);

        $this->assertStringNotContainsString('data-confirm-message', $markup);
    }

    public function testOtherActionsNeverCarryTheAttribute(): void
    {
        foreach (['install', 'enable', 'disable', 'reset', 'uninstall'] as $action) {
            $this->assertStringNotContainsString(
                'data-confirm-message',
                $this->render($action, 'Your settings will be reset.'),
                sprintf('The "%s" action must not carry the update message.', $action)
            );
        }
    }

    public function testTheMessageIsEscapedIntoTheAttribute(): void
    {
        $markup = $this->render('upgrade', 'Read "the docs" & <b>stop</b>');

        $this->assertStringContainsString(
            'data-confirm-message="Read &quot;the docs&quot; &amp; &lt;b&gt;stop&lt;/b&gt;"',
            $markup
        );
    }

    private function render(string $action, string $confirmMessage): string
    {
        return $this->twig->render(self::TEMPLATE, [
            'action' => $action,
            'name' => 'dummy_payment',
            'url' => '/module-action',
            'label' => 'Action',
            'classes' => 'btn',
            'confirm_message' => $confirmMessage,
        ]);
    }
}
