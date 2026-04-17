<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Core\Domain\Attachment;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Domain\Attachment\Exception\EmptySearchInputException;
use PrestaShop\PrestaShop\Core\Domain\Attachment\Query\SearchAttachment;

class SearchAttachmentTest extends TestCase
{
    public function testConstructor(): void
    {
        $search = new SearchAttachment('search');
        $this->assertNotNull($search);
    }

    public function testEmptySearch(): void
    {
        $this->expectException(EmptySearchInputException::class);
        new SearchAttachment('');
    }
}
