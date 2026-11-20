<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Adapter\Module\MailTemplate;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Adapter\Module\MailTemplate\ModuleMailTemplatesSubscriber;
use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\ConfigurationInterface;
use PrestaShop\PrestaShop\Core\Domain\MailTemplate\Command\GenerateThemeMailTemplatesCommand;
use PrestaShop\PrestaShop\Core\Module\ModuleInterface;
use PrestaShopBundle\Entity\Lang;
use PrestaShopBundle\Entity\Repository\LangRepository;
use PrestaShopBundle\Event\ModuleManagementEvent;
use Psr\Log\LoggerInterface;
use RuntimeException;

class ModuleMailTemplatesSubscriberTest extends TestCase
{
    public function testItListensToInstallAndUpgrade(): void
    {
        $this->assertSame(
            [
                ModuleManagementEvent::INSTALL => 'generateMailTemplates',
                ModuleManagementEvent::UPGRADE => 'generateMailTemplates',
            ],
            ModuleMailTemplatesSubscriber::getSubscribedEvents()
        );
    }

    public function testItGeneratesTheConfiguredThemeForEveryLanguageWithoutOverwriting(): void
    {
        $commands = [];
        $commandBus = $this->createMock(CommandBusInterface::class);
        $commandBus->method('handle')->willReturnCallback(function ($command) use (&$commands) {
            $commands[] = $command;

            return null;
        });

        $subscriber = $this->createSubscriber($commandBus, ['en-US', 'fr-FR'], 'classic');
        $subscriber->generateMailTemplates($this->event());

        $this->assertCount(2, $commands);
        foreach ($commands as $command) {
            $this->assertInstanceOf(GenerateThemeMailTemplatesCommand::class, $command);
            $this->assertSame('classic', $command->getThemeName());
            // The whole point: an existing template, possibly customised by the merchant, is kept.
            $this->assertFalse($command->overwriteTemplates());
        }
        $this->assertSame(
            ['en-US', 'fr-FR'],
            array_map(function (GenerateThemeMailTemplatesCommand $command): string {
                return $command->getLanguage();
            }, $commands)
        );
    }

    public function testItFallsBackToTheModernThemeWhenNoneIsConfigured(): void
    {
        $commands = [];
        $commandBus = $this->createMock(CommandBusInterface::class);
        $commandBus->method('handle')->willReturnCallback(function ($command) use (&$commands) {
            $commands[] = $command;

            return null;
        });

        $subscriber = $this->createSubscriber($commandBus, ['en-US'], null);
        $subscriber->generateMailTemplates($this->event());

        $this->assertSame('modern', $commands[0]->getThemeName());
    }

    /**
     * A module is already installed by the time this event is dispatched, so a template that cannot be
     * rendered must be reported rather than thrown - and it must not stop the other languages.
     */
    public function testAFailureIsLoggedAndTheRemainingLanguagesAreStillGenerated(): void
    {
        $handled = 0;
        $commandBus = $this->createMock(CommandBusInterface::class);
        $commandBus->method('handle')->willReturnCallback(function (GenerateThemeMailTemplatesCommand $command) use (&$handled) {
            ++$handled;
            if ('en-US' === $command->getLanguage()) {
                throw new RuntimeException('no such layout');
            }

            return null;
        });

        $logger = $this->createMock(LoggerInterface::class);
        $logger->expects($this->once())
            ->method('error')
            ->with($this->stringContains('no such layout'));

        $subscriber = $this->createSubscriber($commandBus, ['en-US', 'fr-FR'], 'modern', $logger);
        $subscriber->generateMailTemplates($this->event());

        $this->assertSame(2, $handled);
    }

    /**
     * @param string[] $locales
     */
    private function createSubscriber(
        CommandBusInterface $commandBus,
        array $locales,
        ?string $mailTheme,
        ?LoggerInterface $logger = null
    ): ModuleMailTemplatesSubscriber {
        $languages = [];
        foreach ($locales as $locale) {
            $language = $this->createMock(Lang::class);
            $language->method('getLocale')->willReturn($locale);
            $languages[] = $language;
        }

        $langRepository = $this->createMock(LangRepository::class);
        $langRepository->method('findAll')->willReturn($languages);

        $configuration = $this->createMock(ConfigurationInterface::class);
        $configuration->method('get')->with('PS_MAIL_THEME')->willReturn($mailTheme);

        return new ModuleMailTemplatesSubscriber(
            $commandBus,
            $langRepository,
            $configuration,
            $logger ?? $this->createMock(LoggerInterface::class)
        );
    }

    private function event(): ModuleManagementEvent
    {
        $module = $this->createMock(ModuleInterface::class);
        $module->method('get')->with('name')->willReturn('ps_emailalerts');

        return new ModuleManagementEvent($module);
    }
}
