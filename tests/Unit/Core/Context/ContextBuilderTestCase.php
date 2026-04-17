<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Core\Context;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Context\LanguageContext;

abstract class ContextBuilderTestCase extends TestCase
{
    protected function mockLanguageContext(int $languageId): LanguageContext|MockObject
    {
        $languageContext = $this->createMock(LanguageContext::class);
        $languageContext
            ->method('getId')
            ->willReturn($languageId)
        ;

        return $languageContext;
    }
}
