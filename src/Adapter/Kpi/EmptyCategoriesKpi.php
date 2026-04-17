<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Kpi;

use HelperKpi;
use PrestaShop\PrestaShop\Core\ConfigurationInterface;
use PrestaShop\PrestaShop\Core\Kpi\KpiInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class EmptyCategoriesKpi.
 *
 * @internal
 */
final class EmptyCategoriesKpi implements KpiInterface
{
    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var ConfigurationInterface
     */
    private $configuration;

    /**
     * @var string
     */
    private $sourceUrl;

    /**
     * @var string
     */
    private $hrefUrl;

    /**
     * @param TranslatorInterface $translator
     * @param ConfigurationInterface $configuration
     * @param string $sourceUrl
     * @param string $hrefUrl
     */
    public function __construct(
        TranslatorInterface $translator,
        ConfigurationInterface $configuration,
        $sourceUrl,
        $hrefUrl
    ) {
        $this->translator = $translator;
        $this->configuration = $configuration;
        $this->sourceUrl = $sourceUrl;
        $this->hrefUrl = $hrefUrl;
    }

    /**
     * {@inheritdoc}
     */
    public function render()
    {
        $helper = new HelperKpi();
        $helper->id = 'box-empty-categories';
        $helper->icon = 'bookmark';
        $helper->color = 'color2';
        $helper->href = $this->hrefUrl;
        $helper->title = $this->translator->trans('Empty Categories', [], 'Admin.Catalog.Feature');

        if (false !== $this->configuration->get('EMPTY_CATEGORIES')) {
            $helper->value = $this->configuration->get('EMPTY_CATEGORIES');
        }

        $helper->source = $this->sourceUrl;
        $helper->refresh = $this->configuration->get('EMPTY_CATEGORIES_EXPIRE') < time();

        return $helper->generate();
    }
}
