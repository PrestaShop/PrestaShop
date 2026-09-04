<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\PrestaShopBundle\Twig\Layout;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Adapter\LegacyContext;
use PrestaShopBundle\Entity\Repository\TabRepository;
use PrestaShopBundle\Entity\Tab;
use PrestaShopBundle\Routing\Converter\LegacyParametersConverter;
use PrestaShopBundle\Twig\Layout\MenuBuilder;
use ReflectionMethod;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * A tab created by a module carries its label as a wording plus a translation domain, and the
 * label has to be translated when it is displayed - the module's catalogue is not loaded yet at
 * the moment the tab row is written. See issue #30241.
 */
class MenuBuilderTest extends TestCase
{
    public function testTabLabelIsTranslatedFromItsWording(): void
    {
        $translator = $this->createMock(TranslatorInterface::class);
        $translator
            ->expects($this->once())
            ->method('trans')
            ->with('Link List', [], 'Modules.Linklist.Admin')
            ->willReturn('Liste de liens');

        $tab = (new Tab())
            ->setClassName('AdminLinkWidget')
            ->setWording('Link List')
            ->setWordingDomain('Modules.Linklist.Admin');

        $this->assertSame('Liste de liens', $this->getTabLabel($translator, $tab));
    }

    private function getTabLabel(TranslatorInterface $translator, Tab $tab): string
    {
        $menuBuilder = new MenuBuilder(
            $this->createMock(LegacyContext::class),
            $this->createMock(RequestStack::class),
            $this->createMock(TabRepository::class),
            $translator,
            $this->createMock(UrlGeneratorInterface::class),
            $this->createMock(LegacyParametersConverter::class),
        );

        $method = new ReflectionMethod($menuBuilder, 'getTabLabel');
        $method->setAccessible(true);

        return $method->invoke($menuBuilder, $tab);
    }
}
