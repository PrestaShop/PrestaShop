<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Module\MailTemplate;

use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\ConfigurationInterface;
use PrestaShop\PrestaShop\Core\Domain\MailTemplate\Command\GenerateThemeMailTemplatesCommand;
use PrestaShopBundle\Entity\Repository\LangRepository;
use PrestaShopBundle\Event\ModuleManagementEvent;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Throwable;

/**
 * Generates the mail templates a freshly installed module needs, in every language of the shop.
 *
 * Templates are rendered from the mail theme's layouts, and until now that only happened while
 * installing or updating a language. A module installed afterwards therefore had no templates in the
 * languages that already existed, and Mail::send() has no language fallback: it logs "The following
 * e-mail template is missing" and returns false, so those mails are simply never sent.
 *
 * @see https://github.com/PrestaShop/PrestaShop/issues/36734
 */
class ModuleMailTemplatesSubscriber implements EventSubscriberInterface
{
    private const DEFAULT_MAIL_THEME = 'modern';

    /**
     * @var CommandBusInterface
     */
    private $commandBus;

    /**
     * @var LangRepository
     */
    private $langRepository;

    /**
     * @var ConfigurationInterface
     */
    private $configuration;

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(
        CommandBusInterface $commandBus,
        LangRepository $langRepository,
        ConfigurationInterface $configuration,
        LoggerInterface $logger
    ) {
        $this->commandBus = $commandBus;
        $this->langRepository = $langRepository;
        $this->configuration = $configuration;
        $this->logger = $logger;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents(): array
    {
        return [
            ModuleManagementEvent::INSTALL => 'generateMailTemplates',
            // WHY: an upgrade can add a mail layout the previous version did not have, and it lands in
            // the same gap as an install does.
            ModuleManagementEvent::UPGRADE => 'generateMailTemplates',
        ];
    }

    public function generateMailTemplates(ModuleManagementEvent $event): void
    {
        $mailTheme = (string) $this->configuration->get('PS_MAIL_THEME');
        if ('' === $mailTheme) {
            $mailTheme = self::DEFAULT_MAIL_THEME;
        }

        foreach ($this->langRepository->findAll() as $language) {
            try {
                $this->commandBus->handle(new GenerateThemeMailTemplatesCommand(
                    $mailTheme,
                    $language->getLocale(),
                    // WHY: never overwrite. This only fills the gaps left by a module installed after a
                    // language, so a template the merchant has customised through Design > Email theme
                    // must survive every later module installation untouched.
                    false
                ));
            } catch (Throwable $e) {
                // WHY: a template that cannot be rendered must not fail the module installation - the
                // module itself is already installed by the time this event is dispatched. Report it
                // instead, which is more than the silent gap this replaces.
                $this->logger->error(sprintf(
                    'Could not generate the mail templates of module %s for locale %s: %s',
                    $event->getModule()->get('name'),
                    $language->getLocale(),
                    $e->getMessage()
                ));
            }
        }
    }
}
