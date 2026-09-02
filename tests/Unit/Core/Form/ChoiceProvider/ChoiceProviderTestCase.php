<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Tests\Unit\Core\Form\ChoiceProvider;

use PHPUnit\Framework\TestCase;
use Symfony\Contracts\Translation\TranslatorInterface;

abstract class ChoiceProviderTestCase extends TestCase
{
    /**
     * @return TranslatorInterface
     */
    protected function mockTranslator(): TranslatorInterface
    {
        $mock = $this->createMock(TranslatorInterface::class);

        $mock->method('trans')
            ->willReturnArgument(0);

        return $mock;
    }
}
