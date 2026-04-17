<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace Tests\Core\Domain\ImageSettings\CommandHandler;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Adapter\ImageThumbnailsRegenerator;
use PrestaShop\PrestaShop\Core\Domain\ImageSettings\Command\RegenerateThumbnailsCommand;
use PrestaShop\PrestaShop\Core\Domain\ImageSettings\CommandHandler\RegenerateThumbnailsHandler;
use PrestaShop\PrestaShop\Core\Domain\ImageSettings\Exception\RegenerateThumbnailsTimeoutException;
use PrestaShop\PrestaShop\Core\Domain\ImageSettings\Exception\RegenerateThumbnailsWriteException;
use PrestaShop\PrestaShop\Core\Domain\ImageSettings\ImageDomain;
use PrestaShop\PrestaShop\Core\Language\LanguageRepositoryInterface;
use PrestaShopBundle\Entity\Repository\ImageTypeRepository;
use Symfony\Contracts\Translation\TranslatorInterface;

class RegenerateThumbnailsHandlerTest extends TestCase
{
    private TranslatorInterface&MockObject $translator;
    private ImageTypeRepository&MockObject $imageTypeRepository;
    private LanguageRepositoryInterface&MockObject $languageRepository;
    private ImageThumbnailsRegenerator&MockObject $imageThumbnailsRegenerator;
    private RegenerateThumbnailsHandler $handler;

    protected function setUp(): void
    {
        $this->translator = $this->createMock(TranslatorInterface::class);
        $this->imageTypeRepository = $this->createMock(ImageTypeRepository::class);
        $this->languageRepository = $this->createMock(LanguageRepositoryInterface::class);
        $this->imageThumbnailsRegenerator = $this->createMock(ImageThumbnailsRegenerator::class);

        $this->handler = new RegenerateThumbnailsHandler(
            $this->translator,
            $this->imageTypeRepository,
            $this->languageRepository,
            $this->imageThumbnailsRegenerator
        );
    }

    public function testHandleThrowsRegenerateThumbnailsTimeoutException(): void
    {
        $this->languageRepository->expects($this->once())
            ->method('findAll')
            ->willReturn([]);

        $this->imageTypeRepository->expects($this->once())
            ->method('findBy')
            ->willReturn(['dummy_format']);

        $this->imageThumbnailsRegenerator->expects($this->once())
            ->method('regenerateNewImages')
            ->willReturn(['timeout']);

        $command = new RegenerateThumbnailsCommand(ImageDomain::CATEGORIES->value, 0, false);

        $this->expectException(RegenerateThumbnailsTimeoutException::class);

        $this->handler->handle($command);
    }

    public function testHandleThrowsRegenerateThumbnailsWriteException(): void
    {
        $this->languageRepository->expects($this->once())
            ->method('findAll')
            ->willReturn([]);

        $this->imageTypeRepository->expects($this->once())
            ->method('findBy')
            ->willReturn(['dummy_format']);

        $this->imageThumbnailsRegenerator->expects($this->once())
            ->method('regenerateNewImages')
            ->willReturn(['write_error']);

        $command = new RegenerateThumbnailsCommand(ImageDomain::CATEGORIES->value, 0, false);

        $this->expectException(RegenerateThumbnailsWriteException::class);

        $this->handler->handle($command);
    }

    public function testHandleSuccessfulRegeneration(): void
    {
        $languages = [['id_lang' => 1, 'name' => 'English']];

        $this->languageRepository->expects($this->once())
            ->method('findAll')
            ->willReturn($languages);

        $this->imageTypeRepository->expects($this->once())
            ->method('findBy')
            ->willReturn(['dummy_format']);

        $this->imageThumbnailsRegenerator->expects($this->once())
            ->method('regenerateNewImages')
            ->willReturn([]);

        $this->imageThumbnailsRegenerator->expects($this->once())
            ->method('regenerateNoPictureImages')
            ->with(_PS_CAT_IMG_DIR_, ['dummy_format'], $languages)
            ->willReturn(false);

        $command = new RegenerateThumbnailsCommand(ImageDomain::CATEGORIES->value, 0, false);

        $this->handler->handle($command);

        $this->addToAssertionCount(1); // Assert no exceptions were thrown
    }
}
