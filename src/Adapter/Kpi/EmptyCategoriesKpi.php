<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
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
