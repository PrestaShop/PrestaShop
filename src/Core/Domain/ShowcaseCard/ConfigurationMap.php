<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
    public const CLOSED_TEMPLATE = 'PS_SHOWCASECARD_%s_CLOSED';

    /**
     * @var array
     */
    private $closedCardConfiguration;

    public function __construct()
    {
        $this->closedCardConfiguration = [
            ShowcaseCard::SEO_URLS_CARD => sprintf(self::CLOSED_TEMPLATE, 'SEO_URLS'),
            ShowcaseCard::CATEGORIES_CARD => sprintf(self::CLOSED_TEMPLATE, 'CATEGORIES'),
            ShowcaseCard::CUSTOMERS_CARD => sprintf(self::CLOSED_TEMPLATE, 'CUSTOMERS'),
            ShowcaseCard::EMPLOYEES_CARD => sprintf(self::CLOSED_TEMPLATE, 'EMPLOYEES'),
            ShowcaseCard::CMS_PAGES_CARD => sprintf(self::CLOSED_TEMPLATE, 'CMS_PAGES'),
            ShowcaseCard::ATTRIBUTES_CARD => sprintf(self::CLOSED_TEMPLATE, 'ATTRIBUTES'),
            ShowcaseCard::MONITORING_CARD => sprintf(self::CLOSED_TEMPLATE, 'MONITORING'),
            ShowcaseCard::CARRIERS_CARD => sprintf(self::CLOSED_TEMPLATE, 'CARRIERS'),
            ShowcaseCard::FEATURES_CARD => sprintf(self::CLOSED_TEMPLATE, 'FEATURES'),
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
