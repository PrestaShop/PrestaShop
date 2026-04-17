<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Tests\Unit\Core\Translation\Builder\Map;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Translation\Builder\Map\Message;

class MessageTest extends TestCase
{
    public function testGetKey(): void
    {
        $messageTranslation = new Message('theKey');
        $this->assertSame('theKey', $messageTranslation->getKey());
    }

    public function testIsTranslated(): void
    {
        $messageTranslation = new Message('theKey');
        $this->assertFalse($messageTranslation->isTranslated());

        $messageTranslation = new Message('theKey');
        $messageTranslation->setFileTranslation('fileTranslation');
        $this->assertTrue($messageTranslation->isTranslated());

        $messageTranslation = new Message('theKey');
        $messageTranslation->setUserTranslation('userTranslation');
        $this->assertTrue($messageTranslation->isTranslated());

        $messageTranslation = new Message('theKey');
        $messageTranslation->setFileTranslation('fileTranslation');
        $messageTranslation->setUserTranslation('userTranslation');
        $this->assertTrue($messageTranslation->isTranslated());
    }

    public function testGetTranslation(): void
    {
        $messageTranslation = new Message('theKey');
        $this->assertSame('theKey', $messageTranslation->getTranslation());

        $messageTranslation = new Message('theKey');
        $messageTranslation->setFileTranslation('fileTranslation');
        $this->assertSame('fileTranslation', $messageTranslation->getTranslation());

        $messageTranslation = new Message('theKey');
        $messageTranslation->setUserTranslation('userTranslation');
        $this->assertSame('userTranslation', $messageTranslation->getTranslation());

        $messageTranslation = new Message('theKey');
        $messageTranslation->setFileTranslation('fileTranslation');
        $messageTranslation->setUserTranslation('userTranslation');
        $this->assertSame('userTranslation', $messageTranslation->getTranslation());
    }

    public function testMessageContainsTerms(): void
    {
        $messageTranslation = new Message('theKey');
        $this->assertFalse($messageTranslation->contains([]));
        $this->assertFalse($messageTranslation->contains(['fakeTerm']));
        $this->assertFalse($messageTranslation->contains(['the key']));
        $this->assertTrue($messageTranslation->contains(['kEY']));

        $messageTranslation = new Message('theKey');
        $messageTranslation->setFileTranslation('fileTranslation');
        $this->assertFalse($messageTranslation->contains([]));
        $this->assertFalse($messageTranslation->contains(['fakeTerm']));
        $this->assertTrue($messageTranslation->contains(['translation']));
        $this->assertTrue($messageTranslation->contains(['kEY']));

        $messageTranslation = new Message('theKey');
        $messageTranslation->setUserTranslation('userTranslation');
        $this->assertFalse($messageTranslation->contains([]));
        $this->assertFalse($messageTranslation->contains(['fakeTerm']));
        $this->assertTrue($messageTranslation->contains(['translation']));
        $this->assertTrue($messageTranslation->contains(['kEY']));

        $messageTranslation = new Message('theKey');
        $messageTranslation->setFileTranslation('file Translation');
        $messageTranslation->setUserTranslation('userTranslation');
        $this->assertFalse($messageTranslation->contains([]));
        $this->assertFalse($messageTranslation->contains(['fakeTerm']));
        $this->assertTrue($messageTranslation->contains(['file']));
        $this->assertTrue($messageTranslation->contains(['translation']));
        $this->assertTrue($messageTranslation->contains(['kEY']));
    }

    public function testMessageToArray(): void
    {
        $messageTranslation = new Message('theKey');
        $this->assertSame([
            'default' => 'theKey',
            'project' => null,
            'user' => null,
        ], $messageTranslation->toArray());

        $messageTranslation = new Message('theKey');
        $messageTranslation->setFileTranslation('fileTranslation');
        $this->assertSame([
            'default' => 'theKey',
            'project' => 'fileTranslation',
            'user' => null,
        ], $messageTranslation->toArray());

        $messageTranslation = new Message('theKey');
        $messageTranslation->setUserTranslation('userTranslation');
        $this->assertSame([
            'default' => 'theKey',
            'project' => null,
            'user' => 'userTranslation',
        ], $messageTranslation->toArray());

        $messageTranslation = new Message('theKey');
        $messageTranslation->setFileTranslation('file Translation');
        $messageTranslation->setUserTranslation('userTranslation');
        $this->assertSame([
            'default' => 'theKey',
            'project' => 'file Translation',
            'user' => 'userTranslation',
        ], $messageTranslation->toArray());
    }
}
