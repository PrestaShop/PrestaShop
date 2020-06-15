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

namespace PrestaShopBundle\Twig\Extension;

use DateTime;
use DateTimeInterface;
use Twig\Extension\AbstractExtension;
use Twig_SimpleFilter;

class LocalizationExtension extends AbstractExtension
{
    /**
     * @var string
     */
    private $dateFormatFull;

    /**
     * @var string
     */
    private $dateFormatLight;

    /**
     * @param string $contextDateFormatFull
     * @param string $contextDateFormatLight
     */
    public function __construct(string $contextDateFormatFull, string $contextDateFormatLight)
    {
        $this->dateFormatFull = $contextDateFormatFull;
        $this->dateFormatLight = $contextDateFormatLight;
    }

    public function getFilters(): array
    {
        return [
            new Twig_SimpleFilter('date_format_full', [$this, 'dateFormatFull']),
            new Twig_SimpleFilter('date_format_lite', [$this, 'dateFormatLite']),
        ];
    }

    /**
     * @param DateTimeInterface|string $date
     *
     * @return string
     */
    public function dateFormatFull($date): string
    {
        if (!$date instanceof DateTimeInterface) {
            $date = new DateTime($date);
        }

        return $date->format($this->dateFormatFull);
    }

    /**
     * @param DateTimeInterface|string $date
     *
     * @return string
     */
    public function dateFormatLite($date): string
    {
        if (!$date instanceof DateTimeInterface) {
            $date = new DateTime($date);
        }

        return $date->format($this->dateFormatLight);
    }
}
