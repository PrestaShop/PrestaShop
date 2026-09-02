<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Adapter\Configuration;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Adapter\Configuration\PhpParameters;

class PhpParametersTest extends TestCase
{
    /** @var string */
    protected $parametersSampleFile;
    /** @var string */
    protected $parametersSampleFileContent;

    /** @var array */
    protected $sampleParams = [
        'parameters' => [
            'a' => '127.0.0.1',
            'b' => '',
            'c_d' => '1234',
        ],
    ];

    /**
     * {@inheritDoc}
     */
    protected function setUp(): void
    {
        $this->parametersSampleFile = dirname(__DIR__, 2) . '/Resources/config/params.php';
        $this->parametersSampleFileContent = file_get_contents($this->parametersSampleFile);
    }

    /**
     * {@inheritDoc}
     */
    protected function tearDown(): void
    {
        // restore parameters sample file content as it can be modified
        file_put_contents($this->parametersSampleFile, $this->parametersSampleFileContent);
    }

    public function testCannotReadInputFile(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('File a is not readable for configuration');

        new PhpParameters('a');
    }

    public function testGetConfiguration(): void
    {
        $parameterReader = new PhpParameters($this->parametersSampleFile);

        $this->assertEquals($this->sampleParams, $parameterReader->getConfiguration());
    }

    public function testSetProperties(): void
    {
        $parameterReader = new PhpParameters($this->parametersSampleFile);

        $parameterReader->setProperty('a.b.c', 'OSS');
        $parameterReader->setProperty('parameters.b', 'PrestaShop');
        $modifiedParams = $this->sampleParams;
        $modifiedParams['a'] = ['b' => ['c' => 'OSS']];
        $modifiedParams['parameters']['b'] = 'PrestaShop';

        $this->assertEquals($modifiedParams, $parameterReader->getConfiguration());
    }

    public function testSaveConfigurationWithoutModifications(): void
    {
        $parameterReader = new PhpParameters($this->parametersSampleFile);

        $result = $parameterReader->saveConfiguration();

        $this->assertTrue($result);

        $parametersSampleFileContentAfterSave = file_get_contents($this->parametersSampleFile);

        $this->assertParametersPhpFileContentAreEqual(
            $this->parametersSampleFileContent,
            $parametersSampleFileContentAfterSave
        );
    }

    public function testSaveConfigurationWithModifications(): void
    {
        $parameterReader = new PhpParameters($this->parametersSampleFile);
        $parameterReader->setProperty('parameters.b', 'PrestaShop');

        $result = $parameterReader->saveConfiguration();

        $this->assertTrue($result);

        $parametersSampleFileContentModified = file_get_contents(
            dirname(__DIR__, 2) . '/Resources/config/params_modified.php'
        );
        $parametersSampleFileContentAfterSave = file_get_contents($this->parametersSampleFile);

        $this->assertParametersPhpFileContentAreEqual(
            $parametersSampleFileContentModified,
            $parametersSampleFileContentAfterSave
        );
    }

    /**
     * Asserts whether payloads like below are valid, ignoring line breaks and whitespace
     * return array(\n
     *    'parameters' =>\n
     *        array(\n
     *            'a' => '127.0.0.1',\n
     *            'c_d' => '1234',\n
     *       ),\n
     *
     * @param string $fileContent1
     * @param string $fileContent2
     */
    protected function assertParametersPhpFileContentAreEqual(string $fileContent1, string $fileContent2): void
    {
        $this->assertEquals(
            str_replace(["\n", ' '], ['', ''], $fileContent1),
            str_replace(["\n", ' '], ['', ''], $fileContent2)
        );
    }
}
