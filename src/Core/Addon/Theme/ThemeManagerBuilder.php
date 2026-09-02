<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Addon\Theme;

use Context;
use Db;
use Employee;
use PrestaShop\PrestaShop\Adapter\Configuration;
use PrestaShop\PrestaShop\Adapter\Hook\HookInformationProvider;
use PrestaShop\PrestaShop\Core\Addon\Module\ModuleManagerBuilder;
use PrestaShop\PrestaShop\Core\Context\ApiClientContext;
use PrestaShop\PrestaShop\Core\Image\ImageTypeRepository;
use PrestaShop\PrestaShop\Core\Module\HookConfigurator;
use PrestaShop\PrestaShop\Core\Module\HookRepository;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Shop;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;

class ThemeManagerBuilder
{
    private LoggerInterface $logger;
    private ApiClientContext $apiClientContext;

    public function __construct(
        private Context $context,
        private readonly Db $db,
        private ?ThemeValidator $themeValidator = null,
        ?LoggerInterface $logger = null,
        ?ApiClientContext $apiClientContext = null
    ) {
        $this->logger = $logger ?? new NullLogger();
        $this->apiClientContext = $apiClientContext ?: new ApiClientContext(null);
    }

    public function build()
    {
        $configuration = new Configuration();
        $configuration->restrictUpdatesTo($this->context->shop);
        if (null === $this->themeValidator) {
            $this->themeValidator = new ThemeValidator($this->context->getTranslator(), new Configuration());
        }
        if (null === $this->context->employee) {
            $this->context->employee = new Employee();
        }

        $moduleManagerBuilder = ModuleManagerBuilder::getInstance();
        $moduleManager = $moduleManagerBuilder->build();

        return new ThemeManager(
            $this->context->shop,
            $configuration,
            $this->themeValidator,
            $this->context->getTranslator(),
            $this->context->employee,
            new Filesystem(),
            new Finder(),
            new HookConfigurator(
                new HookRepository(
                    new HookInformationProvider(),
                    $this->context->shop,
                    $this->db
                ),
                $this->logger,
                $moduleManager,
            ),
            $this->buildRepository($this->context->shop),
            new ImageTypeRepository($this->db),
            $this->logger,
            $this->apiClientContext,
        );
    }

    public function buildRepository(?Shop $shop = null)
    {
        if (!$shop instanceof Shop) {
            $shop = $this->context->shop;
        }

        $configuration = new Configuration();
        $configuration->restrictUpdatesTo($shop);

        return new ThemeRepository(
            $configuration,
            new Filesystem(),
            $shop
        );
    }
}
