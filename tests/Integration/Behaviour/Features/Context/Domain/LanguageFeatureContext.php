<?php

namespace Tests\Integration\Behaviour\Features\Context\Domain;

use Behat\Behat\Tester\Exception\PendingException;
use Behat\Gherkin\Node\TableNode;
use PrestaShop\PrestaShop\Core\Domain\Language\Command\AddLanguageCommand;
use PrestaShop\PrestaShop\Core\Domain\Language\ValueObject\LanguageId;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Tests\Integration\Behaviour\Features\Context\SharedStorage;
use Tests\Integration\Behaviour\Features\Context\Util\PrimitiveUtils;

/**
 * Class LanguageFeatureContext
 */
class LanguageFeatureContext extends AbstractDomainFeatureContext
{
    private const SHOP_ASSOCIATION = [];
    private const JPG_IMAGE_STRING = 'iVBORw0KGgoAAAANSUhEUgAAABwAAAASCAMAAAB/2U7WAAAABl'
        . 'BMVEUAAAD///+l2Z/dAAAASUlEQVR4XqWQUQoAIAxC2/0vXZDr'
        . 'EX4IJTRkb7lobNUStXsB0jIXIAMSsQnWlsV+wULF4Avk9fLq2r'
        . '8a5HSE35Q3eO2XP1A1wQkZSgETvDtKdQAAAABJRU5ErkJggg==';
    const JPG_IMAGE_TYPE = '.jpg';
    const DEFAULT_LANGUAGE_ID = 'test1';

    /** @var ContainerInterface */
    private $container;

    public function __construct()
    {
        $this->container = $this->getContainer();
    }

    /**
     * @When I add new language :languageReference with following details:
     *
     * @param string $languageReference
     * @param TableNode $table
     */
    public function addNewLanguageWithFollowingDetails(string $languageReference, TableNode $table)
    {
        $testCaseData = $table->getRowsHash();
        $this->pretendImagesUploaded();

        /** @var LanguageId $languageId */
        $languageId = $this->getCommandBus()->handle(new AddLanguageCommand(
            $testCaseData['Name'],
            $testCaseData['ISO code'],
            $testCaseData['Language code'],
            $testCaseData['Date format'],
            $testCaseData['Date format (full)'],
            $testCaseData['Flag'],
            $testCaseData['"No-picture" image'],
            PrimitiveUtils::castElementInType($testCaseData['Is RTL language'], PrimitiveUtils::TYPE_BOOLEAN),
            PrimitiveUtils::castElementInType($testCaseData['Status'], PrimitiveUtils::TYPE_BOOLEAN),
            self::SHOP_ASSOCIATION
        ));

        SharedStorage::getStorage()->set($languageReference, $languageId->getValue());
    }

    /**
     * @Then I should be able to see :languageReference language edit form with following details:
     *
     * @param $languageReference
     * @param TableNode $table
     */
    public function thereIsLanguageWithFollowingDetails($languageReference, TableNode $table)
    {
        throw new PendingException();
    }

    private function pretendImagesUploaded()
    {
        $data = base64_decode(self::JPG_IMAGE_STRING);
        $im = imagecreatefromstring($data);
        if ($im !== false) {
            header('Content-Type: image/jpg');
            imagejpeg(
                $im,
                _PS_LANG_IMG_DIR_ . self::DEFAULT_LANGUAGE_ID . self::JPG_IMAGE_TYPE,
                0
            );
            imagedestroy($im);
        }
    }
}
