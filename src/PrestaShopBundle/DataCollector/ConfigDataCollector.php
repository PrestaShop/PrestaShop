<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\DataCollector;

use PrestaShop\PrestaShop\Core\Foundation\Version;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\DataCollector\ConfigDataCollector as SymfonyDataCollector;
use Throwable;

/**
 * Class ConfigDataCollector.
 * This class is used to collect some data for the WebProfiler.
 */
class ConfigDataCollector extends SymfonyDataCollector
{
    public function __construct(
        private readonly string $name,
        private readonly Version $version
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public function collect(Request $request, Response $response, ?Throwable $exception = null): void
    {
        parent::collect($request, $response, $exception);
        $this->data['app_name'] = $this->name;
        $this->data['app_version'] = $this->version->getSemVersion();
    }

    /**
     * Get the application name.
     */
    public function getApplicationName(): string
    {
        return $this->data['app_name'];
    }

    /**
     * Get the application version.
     */
    public function getApplicationVersion(): string
    {
        return $this->data['app_version'];
    }
}
