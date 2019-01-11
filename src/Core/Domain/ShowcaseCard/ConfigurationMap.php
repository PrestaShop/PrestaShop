<?php

/**
 * 2007-2019 PrestaShop SA and Contributors
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
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Core\Domain\ShowcaseCard;

use PrestaShop\PrestaShop\Core\Domain\ShowcaseCard\Exception\ShowcaseCardException;
use PrestaShop\PrestaShop\Core\Domain\ShowcaseCard\ValueObject\ShowcaseCard;

/**
 * Maps showcase card names to configuration names from ps_configuration
 */
class ConfigurationMap
{
    /**
     * Template used to create configuration names for "closed status"
     */
    const CLOSED_TEMPLATE = 'PS_SHOWCASECARD_%s_CLOSED';

    /**
     * @var array
     */
    private $closedCardConfiguration;

    public function __construct()
    {
        $this->closedCardConfiguration = [
            ShowcaseCard::SEO_URLS_CARD => sprintf(self::CLOSED_TEMPLATE, 'SEO_URLS'),
        ];
    }

    /**
     * Returns the ps_configuration configuration name for "closed status" of the provided card
     *
     * @param ShowcaseCard $cardName
     *
     * @return string
     *
     * @throws ShowcaseCardException If there's no configuration for that showcase card
     */
    public function getConfigurationNameForClosedStatus(ShowcaseCard $cardName)
    {
        $name = $cardName->getName();

        if (!isset($this->closedCardConfiguration[$name])) {
            throw new ShowcaseCardException(sprintf('No closed status configuration found for showcase card "%s"', $name));
        }

        return $this->closedCardConfiguration[$name];
    }
}
