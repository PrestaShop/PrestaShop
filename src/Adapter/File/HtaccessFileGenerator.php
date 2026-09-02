<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\File;

use PrestaShop\PrestaShop\Adapter\Tools;
use PrestaShop\PrestaShop\Core\Cache\Clearer\CacheClearerInterface;

/**
 * Class HtaccessFileGenerator is responsible for generating htaccess file with its default content.
 */
class HtaccessFileGenerator
{
    /**
     * @var CacheClearerInterface
     */
    private $cacheClearer;

    /**
     * @var Tools
     */
    private $tools;

    /**
     * @var bool
     */
    private $multipleViewsConfiguration;

    /**
     * HtaccessFileGenerator constructor.
     *
     * @param CacheClearerInterface $cacheClearer
     * @param Tools $tools
     * @param bool $multipleViewsConfiguration
     */
    public function __construct(CacheClearerInterface $cacheClearer, Tools $tools, $multipleViewsConfiguration)
    {
        $this->cacheClearer = $cacheClearer;
        $this->tools = $tools;
        $this->multipleViewsConfiguration = $multipleViewsConfiguration;
    }

    /**
     * Generates htaccess file and its content.
     *
     * @param bool|null $disableMultiView if null, rely on the Shop configuration
     *
     * @return bool
     */
    public function generateFile($disableMultiView = null)
    {
        if (null === $disableMultiView) {
            $disableMultiView = $this->multipleViewsConfiguration;
        }

        $isGenerated = $disableMultiView ? $this->tools->generateHtaccessWithMultiViews() : $this->tools->generateHtaccessWithoutMultiViews();

        if ($isGenerated) {
            $this->cacheClearer->clear();
        }

        return $isGenerated;
    }
}
