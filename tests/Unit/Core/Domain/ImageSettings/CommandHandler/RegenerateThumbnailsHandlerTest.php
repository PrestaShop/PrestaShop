<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
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
