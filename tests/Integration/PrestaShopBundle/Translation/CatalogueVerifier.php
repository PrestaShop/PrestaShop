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

namespace Tests\Integration\PrestaShopBundle\Translation;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Translation\MessageCatalogue;
use Symfony\Component\Translation\MessageCatalogueInterface;

/**
 * Used to match a MessageCatalogue against an expected one
 */
class CatalogueVerifier
{
    /**
     * @var TestCase
     */
    private $test;

    /**
     * @param TestCase $test The test class
     */
    public function __construct(TestCase $test)
    {
        $this->test = $test;
    }

    /**
     * Verifies that the provided catalogue contains all the strings and domains as defined in $expected
     *
     * @param MessageCatalogueInterface $messageCatalogue The catalogue to test
     * @param array[] $expected An array of domainName => messages
     */
    public function assertCataloguesMatch(MessageCatalogueInterface $messageCatalogue, $expected)
    {
        $domains = $messageCatalogue->getDomains();

        foreach ($expected as $expectedDomain => $expectedStrings) {
            // the domain should be defined
            $this->test->assertContains(
                $expectedDomain,
                $domains,
                sprintf('Domain "%s" is not defined in %s', $expectedDomain, print_r($domains, true))
            );

            // all strings should be defined in the appropriate domain
            foreach ($expectedStrings as $key => $string) {
                $this->test->assertTrue(
                    $messageCatalogue->defines($key, $expectedDomain),
                    sprintf('"%s" not found in %s', $string, $expectedDomain)
                );

                $this->test->assertSame(
                    $messageCatalogue->get($key, $expectedDomain),
                    $string,
                    sprintf(
                        'The translation result for "%s" was expected to be "%s" but was "%s',
                        $key,
                        $string,
                        $messageCatalogue->get($key, $expectedDomain)
                    )
                );
            }
        }
    }
}
