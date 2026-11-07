<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace Tests\Unit\PrestaShopBundle\Resources\Config;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Yaml\Yaml;

class AdminActivityFormHandlerConfigurationTest extends TestCase
{
    public function testProductAndCategoryFormHandlersOptInToActivityLogging(): void
    {
        $configuration = Yaml::parseFile(
            dirname(__DIR__, 5) . '/src/PrestaShopBundle/Resources/config/services/core/form/form_handler.yml'
        );

        $services = $configuration['services'];

        $this->assertSame(
            'Product',
            $services['prestashop.core.form.identifiable_object.product_form_handler']['arguments'][1]
        );
        $this->assertSame(
            'Category',
            $services['prestashop.core.form.identifiable_object.handler.category_form_handler']['arguments'][1]
        );
        $this->assertSame(
            'Category',
            $services['prestashop.core.form.identifiable_object.handler.root_category_form_handler']['arguments'][1]
        );
    }

    public function testUnrelatedFormHandlerDoesNotOptInImplicitly(): void
    {
        $configuration = Yaml::parseFile(
            dirname(__DIR__, 5) . '/src/PrestaShopBundle/Resources/config/services/core/form/form_handler.yml'
        );

        $arguments = $configuration['services']['prestashop.core.form.identifiable_object.handler.contact_form_handler']['arguments'];

        $this->assertCount(1, $arguments);
    }
}
