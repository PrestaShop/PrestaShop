<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace Tests\Integration\Behaviour\Features\Context\Domain;

use Behat\Behat\Context\Context;
use Currency;
use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Tests\Integration\Behaviour\Features\Context\AbstractPrestaShopFeatureContext;
use Tests\Integration\Behaviour\Features\Context\CommonFeatureContext;
use Tests\Integration\Behaviour\Features\Context\LastExceptionTrait;
use Tests\Resources\MailDevClient;

abstract class AbstractDomainFeatureContext extends AbstractPrestaShopFeatureContext implements Context
{
    use LastExceptionTrait;

    protected const JPG_IMAGE_TYPE = '.jpg';
    protected const JPG_IMAGE_STRING = 'iVBORw0KGgoAAAANSUhEUgAAABwAAAASCAMAAAB/2U7WAAAABl'
        . 'BMVEUAAAD///+l2Z/dAAAASUlEQVR4XqWQUQoAIAxC2/0vXZDr'
        . 'EX4IJTRkb7lobNUStXsB0jIXIAMSsQnWlsV+wULF4Avk9fLq2r'
        . '8a5HSE35Q3eO2XP1A1wQkZSgETvDtKdQAAAABJRU5ErkJggg==';

    /**
     * @return CommandBusInterface
     */
    protected function getCommandBus()
    {
        return CommonFeatureContext::getContainer()->get('prestashop.core.command_bus');
    }

    /**
     * @return CommandBusInterface
     */
    protected function getQueryBus()
    {
        return CommonFeatureContext::getContainer()->get('prestashop.core.query_bus');
    }

    protected function getContainer(): ContainerInterface
    {
        return CommonFeatureContext::getContainer();
    }

    protected function getMailDevClient(): MailDevClient
    {
        return $this->getContainer()->get(MailDevClient::class);
    }

    protected function getDefaultCurrencyId(): int
    {
        return Currency::getDefaultCurrencyId();
    }

    protected function getDefaultCurrencyIsoCode(): string
    {
        return Currency::getIsoCodeById($this->getDefaultCurrencyId());
    }

    /**
     * @param string $dirImage
     * @param string $imageName
     * @param int $objectId
     *
     * @return string
     */
    protected function pretendImageUploaded(string $dirImage, string $imageName, int $objectId): string
    {
        // @todo: refactor CategoryCoverUploader. Move uploaded file in Form handler instead of Uploader and use the uploader here in tests
        $im = imagecreatefromstring(base64_decode(self::JPG_IMAGE_STRING));
        if ($im !== false) {
            header('Content-Type: image/jpg');
            imagejpeg(
                $im,
                $dirImage . $objectId . self::JPG_IMAGE_TYPE,
                0
            );
            imagedestroy($im);
        }

        return $imageName;
    }
}
