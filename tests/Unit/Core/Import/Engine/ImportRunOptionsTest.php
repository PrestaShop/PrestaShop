<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Core\Import\Engine;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Import\Engine\ImportRunOptions;

class ImportRunOptionsTest extends TestCase
{
    public function testCoreOptionsAreTypedAndDefaultToFalse(): void
    {
        $options = ImportRunOptions::fromArray(['forceIds' => '1', 'matchRef' => true]);

        $this->assertTrue($options->forceIds, 'Truthy JSON values must be cast to bool');
        $this->assertTrue($options->matchRef);
        $this->assertFalse($options->truncate);
        $this->assertFalse($options->sendEmail);
        $this->assertFalse($options->dryRun);
        $this->assertSame([], $options->getExtra());
    }

    /**
     * The whole point of keeping unknown keys: the run context is rebuilt from
     * the database on every batch request, so an option toArray() dropped would
     * silently vanish between two batches — and an importer shipped by a module
     * could never receive one.
     */
    public function testImporterSpecificOptionsSurviveTheRoundTrip(): void
    {
        $stored = [
            'matchRef' => true,
            'mymodule_strategy' => 'upsert',
            'mymodule_threshold' => 42,
            'mymodule_flags' => ['a', 'b'],
        ];

        $roundTripped = ImportRunOptions::fromArray(ImportRunOptions::fromArray($stored)->toArray())->toArray();

        foreach ($stored as $key => $value) {
            $this->assertSame($value, $roundTripped[$key], sprintf('Option "%s" must survive being persisted and rebuilt', $key));
        }
    }

    public function testUnknownOptionsAreReadableAndSeparableFromTheCoreOnes(): void
    {
        $options = ImportRunOptions::fromArray(['truncate' => true, 'mymodule_strategy' => 'upsert']);

        $this->assertSame(['mymodule_strategy' => 'upsert'], $options->getExtra(), 'getExtra() must not leak the core options');

        $this->assertTrue($options->has('mymodule_strategy'));
        $this->assertSame('upsert', $options->get('mymodule_strategy'));

        // get()/has() cover the core options too, so an importer can read any
        // option by name without caring who declared it
        $this->assertTrue($options->has('truncate'));
        $this->assertTrue($options->get('truncate'));

        $this->assertFalse($options->has('never_declared'));
        $this->assertNull($options->get('never_declared'));
        $this->assertSame('fallback', $options->get('never_declared', 'fallback'));
    }

    public function testACoreKeyCannotBeShadowedByAnExtraOne(): void
    {
        // 'truncate' is a core key, so it must land on the typed property and
        // NOT be duplicated into the extra bag
        $options = new ImportRunOptions(truncate: true, extra: ['truncate' => false]);

        $this->assertTrue($options->toArray()['truncate'], 'The typed core option must win over a stray extra key');
    }
}
