<?php
/**
 * 2007-2020 PrestaShop SA and Contributors
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2020 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Adapter\Kpi;

use HelperKpi;
use PrestaShop\PrestaShop\Core\ConfigurationInterface;
use PrestaShop\PrestaShop\Core\Kpi\KpiInterface;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Class TopCategoryKpi.
 *
 * @internal
 */
final class TopCategoryKpi implements KpiInterface
{
    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var ConfigurationInterface
     */
    private $kpiConfiguration;

    /**
     * @var string
     */
    private $sourceUrl;

    /**
     * @var int
     */
    private $employeeIdLang;

    /**
     * @param TranslatorInterface $translator
     * @param ConfigurationInterface $kpiConfiguration
     * @param string $sourceUrl
     * @param int $employeeIdLang
     */
    public function __construct(
        TranslatorInterface $translator,
        ConfigurationInterface $kpiConfiguration,
        $sourceUrl,
        $employeeIdLang
    ) {
        $this->translator = $translator;
        $this->kpiConfiguration = $kpiConfiguration;
        $this->sourceUrl = $sourceUrl;
        $this->employeeIdLang = $employeeIdLang;
    }

    /**
     * {@inheritdoc}
     */
    public function render()
    {
        $helper = new HelperKpi();
        $helper->id = 'box-top-category';
        $helper->icon = 'money';
        $helper->color = 'color3';
        $helper->title = $this->translator->trans('Top Category', [], 'Admin.Catalog.Feature');
        $helper->subtitle = $this->translator->trans('30 days', [], 'Admin.Global');

        $topCategory = $this->kpiConfiguration->get('TOP_CATEGORY');

        if (isset($topCategory[$this->employeeIdLang])) {
            $helper->value = $topCategory[$this->employeeIdLang];
        }

        $topCategoryExpire = $this->kpiConfiguration->get('TOP_CATEGORY_EXPIRE');
        if (isset($topCategoryExpire[$this->employeeIdLang])) {
            $topCategoryExpire = $topCategoryExpire[$this->employeeIdLang];
        } else {
            $topCategoryExpire = false;
        }

        $helper->source = $this->sourceUrl;
        $helper->refresh = $topCategoryExpire < time();

        return $helper->generate();
    }
}
