<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Classes\Webservice;

use PHPUnit\Framework\TestCase;
use WebserviceOutputXMLCore;

class WebserviceOutputXMLTest extends TestCase
{
    private function getRenderer(): WebserviceOutputXMLCore
    {
        return new WebserviceOutputXMLCore([1]);
    }

    public function testErrorMessagesContainingCdataTerminatorStayVerbatim()
    {
        $message = 'invalid key ]]> for filter ]]>value';

        $xml = simplexml_load_string(
            '<errors>' . $this->getRenderer()->renderErrors($message, 42) . '</errors>'
        );

        $this->assertNotFalse($xml);
        $this->assertSame($message, (string) $xml->error->message);
        $this->assertSame('42', (string) $xml->error->code);
    }

    public function testFieldValuesContainingCdataTerminatorStayVerbatim()
    {
        $value = 'stored ]]> data ]]> with terminators';

        $xml = simplexml_load_string(
            '<resource>' . $this->getRenderer()->renderField([
                'sqlId' => 'description',
                'value' => $value,
            ]) . '</resource>'
        );

        $this->assertNotFalse($xml);
        $this->assertSame($value, (string) $xml->description);
    }

    public function testI18nFieldValuesContainingCdataTerminatorStayVerbatim()
    {
        $value = 'traduction ]]> avec terminateur';

        $xml = simplexml_load_string(
            '<resource xmlns:xlink="http://www.w3.org/1999/xlink">' . $this->getRenderer()->renderField([
                'sqlId' => 'description',
                'i18n' => true,
                'value' => [1 => $value],
            ]) . '</resource>'
        );

        $this->assertNotFalse($xml);
        $this->assertSame($value, (string) $xml->description->language);
    }
}
