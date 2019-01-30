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
    const SEO_URLS_CARD = 'seo-urls_card';

    /**
     * List of supported card names
     */
    const SUPPORTED_NAMES = [
        self::SEO_URLS_CARD => true,
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
            throw new InvalidShowcaseCardNameException(
                sprintf('Unsupported showcase card name: %s', print_r($name, true))
            );
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
