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
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Kpi;

use ConfigurationKPI;
use HelperKpi;
use PrestaShop\PrestaShop\Adapter\LegacyContext;
use PrestaShop\PrestaShop\Core\Kpi\KpiInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class AverageResponseTimeKpi implements KpiInterface
{
    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var LegacyContext
     */
    private $context;

    public function __construct(TranslatorInterface $translator, LegacyContext $context)
    {
        $this->translator = $translator;
        $this->context = $context;
    }

    public function render(): string
    {
        $time = time();

        $helper = new HelperKpi();
        $helper->id = 'box-age';
        $helper->icon = 'watch';
        $helper->color = 'color2';
        $helper->title = $this->translator->trans('Average Response Time', [], 'Admin.Catalog.Feature');
        $helper->subtitle = $this->translator->trans('30 days', [], 'Admin.Global');
        if (ConfigurationKPI::get('AVG_MSG_RESPONSE_TIME') !== false) {
            $helper->value = ConfigurationKPI::get('AVG_MSG_RESPONSE_TIME');
        }
        $helper->source = $this->context->getAdminLink(
            'AdminStats',
                true,
                [
                    'ajax' => 1,
                    'action' => 'getKpi',
                    'kpi' => 'avg_msg_response_time',
                ]
            );
        $helper->refresh = (bool) (ConfigurationKPI::get('AVG_MSG_RESPONSE_TIME_EXPIRE') < $time);

        return $helper->generate();
    }
}
