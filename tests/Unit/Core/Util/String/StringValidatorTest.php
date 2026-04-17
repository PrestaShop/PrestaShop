<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace Tests\Unit\Core\Util\String;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Util\String\StringValidator;

class StringValidatorTest extends TestCase
{
    public function testItDetectsThatStringStartsWith()
    {
        $data = [
            [
                'string' => 'startString',
                'starts_with' => 'start',
                'expects' => true,
            ],
            [
                'string' => 'start another start string',
                'starts_with' => 'start',
                'expects' => true,
            ],
            [
                'string' => ' start',
                'starts_with' => 'start',
                'expects' => false,
            ],
        ];

        $stringValidator = new StringValidator();

        foreach ($data as $item) {
            $result = $stringValidator->startsWith($item['string'], $item['starts_with']);

            $this->assertEquals($item['expects'], $result);
        }
    }

    public function testItDetectsThatStringEndsWith()
    {
        $data = [
            [
                'string' => 'startString',
                'ends_with' => 'String',
                'expects' => true,
            ],
            [
                'string' => 'start . another . string.',
                'ends_with' => '.',
                'expects' => true,
            ],
            [
                'string' => ' start.',
                'ends_with' => 'start',
                'expects' => false,
            ],
        ];

        $stringValidator = new StringValidator();

        foreach ($data as $item) {
            $result = $stringValidator->endsWith($item['string'], $item['ends_with']);

            $this->assertEquals($item['expects'], $result);
        }
    }

    public function testStringStartsWithAndEndsWith()
    {
        $data = [
            [
                'string' => 'actionSomethingModifier',
                'prefix' => 'action',
                'suffix' => 'Modifier',
                'expects' => true,
            ],
            [
                'string' => 'start . another . string.',
                'prefix' => 'start',
                'suffix' => '. string.',
                'expects' => true,
            ],
            [
                'string' => ' start.',
                'prefix' => ' ',
                'suffix' => 't',
                'expects' => false,
            ],
        ];

        $stringValidator = new StringValidator();

        foreach ($data as $item) {
            $result = $stringValidator->startsWithAndEndsWith(
                $item['string'],
                $item['prefix'],
                $item['suffix']
            );

            $this->assertEquals($item['expects'], $result);
        }
    }

    public function testStringContainsWhiteSpaces()
    {
        $data = [
            [
                'string' => 'action Something Modifier',
                'expects' => true,
            ],
            [
                'string' => 'tab    ',
                'expects' => true,
            ],
            [
                'string' => 'noWhiteSpace',
                'expects' => false,
            ],
        ];

        $stringValidator = new StringValidator();

        foreach ($data as $item) {
            $result = $stringValidator->doesContainsWhiteSpaces($item['string']);
            $this->assertEquals($item['expects'], $result);
        }
    }
}
