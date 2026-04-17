<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\PrestaShopBundle\Controller\Api;

class I18nControllerTest extends ApiTestCase
{
    /**
     * @dataProvider getBadListTranslations
     *
     * @param array $params
     */
    public function testItShouldReturnBadResponseWhenRequestingListOfTranslations(array $params): void
    {
        $this->assertBadRequest('api_i18n_translations_list', $params);
    }

    /**
     * @return array
     */
    public function getBadListTranslations(): array
    {
        return [
            [
                ['page' => 'internationnal'], // syntax error wanted
            ],
            [
                ['page' => 'stockk'], // syntax error wanted
            ],
        ];
    }

    /**
     * @dataProvider getGoodListTranslations
     *
     * @param array $params
     */
    public function testItShouldReturnOkResponseWhenRequestingListOfTranslations(array $params): void
    {
        $this->assertOkRequest('api_i18n_translations_list', $params);
    }

    /**
     * @return array
     */
    public function getGoodListTranslations(): array
    {
        return [
            [
                ['page' => 'international'],
            ],
            [
                ['page' => 'stock'],
            ],
        ];
    }
}
