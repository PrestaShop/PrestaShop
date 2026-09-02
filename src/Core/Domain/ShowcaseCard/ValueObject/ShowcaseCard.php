<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\ShowcaseCard\ValueObject;

use PrestaShop\PrestaShop\Core\Domain\ShowcaseCard\Exception\InvalidShowcaseCardNameException;

/**
 * Showcase cards are help dialogs that appear at the top of pages to guide the merchant
 */
class ShowcaseCard
{
    /**
     * Card shown in SEO & URLs
     */
    public const SEO_URLS_CARD = 'seo-urls_card';

    /**
     * Card shown in Categories
     */
    public const CATEGORIES_CARD = 'categories_card';

    /**
     * Card shown in Customers
     */
    public const CUSTOMERS_CARD = 'customers_card';

    /**
     * Card shown in Employees
     */
    public const EMPLOYEES_CARD = 'employees_card';

    /**
     * Card shown in Improve -> Design -> Pages
     */
    public const CMS_PAGES_CARD = 'cms-pages_card';

    /**
     * Card shown in Sell -> Catalog -> Attributes & features -> Attributes
     */
    public const ATTRIBUTES_CARD = 'attributes_card';

    /**
     * Card shown in Sell -> Catalog -> Monitoring
     */
    public const MONITORING_CARD = 'monitoring_card';

    /**
     * Card shown in Improve -> Shipping -> Carriers
     */
    public const CARRIERS_CARD = 'carriers_card';

    /**
     * Card shown in Sell -> Catalog -> Attributes & features -> Features
     */
    public const FEATURES_CARD = 'features_card';

    /**
     * List of supported card names
     */
    public const SUPPORTED_NAMES = [
        self::SEO_URLS_CARD => true,
        self::CATEGORIES_CARD => true,
        self::CUSTOMERS_CARD => true,
        self::EMPLOYEES_CARD => true,
        self::CMS_PAGES_CARD => true,
        self::ATTRIBUTES_CARD => true,
        self::MONITORING_CARD => true,
        self::CARRIERS_CARD => true,
        self::FEATURES_CARD => true,
    ];

    /**
     * @var string
     */
    private $name;

    /**
     * ShowcaseCardName constructor.
     *
     * @param string $name Showcase card name
     *
     * @throws InvalidShowcaseCardNameException
     */
    public function __construct($name)
    {
        if (!$this->isSupported($name)) {
            throw new InvalidShowcaseCardNameException(sprintf('Unsupported showcase card name: %s', print_r($name, true)));
        }

        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Indicates if the provided name matches an existing showcase card
     *
     * @param string $name
     *
     * @return bool
     */
    private function isSupported($name)
    {
        return array_key_exists($name, self::SUPPORTED_NAMES);
    }
}
