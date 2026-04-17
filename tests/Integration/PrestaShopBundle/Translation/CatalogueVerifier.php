<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
