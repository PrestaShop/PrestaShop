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

namespace Tests\Unit\PrestaShopBundle\Entity\Repository;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\ClassMetadata;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Language\LanguageInterface;
use PrestaShop\PrestaShop\Core\Language\LanguageRepositoryInterface;
use PrestaShopBundle\Entity\Repository\LangRepository;

/**
 * This unit test is a bit twisted, it tests the internal implementation of the
 * LangRepository class which extends doctrine EntityRepository class.
 * The purpose of this test is to check that the request is performed only once,
 * that's why we mock only the findOneBy method.
 * IMPORTANT NOTE: if the internal implementation of LangRepository was to change
 * and this test loses its purpose feel free to remove it.
 */
class LangRepositoryTest extends TestCase
{
    public function testInternalCacheByLocale()
    {
        /** @var LanguageInterface $languageMock */
        $languageMock = $this->buildLanguageMock();
        /** @var LangRepository $partialMock */
        $partialMock = $this->buildPartialMock(
            [LangRepository::LOCALE => 'en-US'],
            $languageMock
        );
        $this->assertInstanceOf(LangRepository::class, $partialMock);
        $this->assertInstanceOf(LanguageRepositoryInterface::class, $partialMock);

        $language = $partialMock->getOneByLocale('en-US');
        $this->assertNotNull($language);
        $this->assertInstanceOf(LanguageInterface::class, $language);
        $this->assertEquals('en-US', $language->getLocale());
        $this->assertEquals($languageMock, $language);

        //Second call does not call findOneBy (cached result)
        $language = $partialMock->getOneByLocale('en-US');
        $this->assertNotNull($language);
        $this->assertInstanceOf(LanguageInterface::class, $language);
        $this->assertEquals('en-US', $language->getLocale());
        $this->assertEquals($languageMock, $language);

        //Third call by iso code still does not call findOneBy (cached result)
        $language = $partialMock->getOneByIsoCode('en');
        $this->assertNotNull($language);
        $this->assertInstanceOf(LanguageInterface::class, $language);
        $this->assertEquals('en', $language->getIsoCode());
        $this->assertEquals($languageMock, $language);
    }

    public function testInternalCacheByIsoCode()
    {
        /** @var LanguageInterface $languageMock */
        $languageMock = $this->buildLanguageMock();
        /** @var LangRepository $partialMock */
        $partialMock = $this->buildPartialMock(
            [LangRepository::ISO_CODE => 'en'],
            $languageMock
        );
        $this->assertInstanceOf(LangRepository::class, $partialMock);
        $this->assertInstanceOf(LanguageRepositoryInterface::class, $partialMock);

        $language = $partialMock->getOneByIsoCode('en');
        $this->assertNotNull($language);
        $this->assertInstanceOf(LanguageInterface::class, $language);
        $this->assertEquals('en', $language->getIsoCode());
        $this->assertEquals($languageMock, $language);

        //Second call does not call findOneBy (cached result)
        $language = $partialMock->getOneByLocale('en-US');
        $this->assertNotNull($language);
        $this->assertInstanceOf(LanguageInterface::class, $language);
        $this->assertEquals('en', $language->getIsoCode());
        $this->assertEquals($languageMock, $language);

        //Third call by iso code still does not call findOneBy (cached result)
        $language = $partialMock->getOneByIsoCode('en');
        $this->assertNotNull($language);
        $this->assertInstanceOf(LanguageInterface::class, $language);
        $this->assertEquals('en', $language->getIsoCode());
        $this->assertEquals($languageMock, $language);
    }

    public function testInternalAbsentCache()
    {
        $entityManagerMock = $this->getMockBuilder(EntityManager::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $classMetadataMock = $this->getMockBuilder(ClassMetadata::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        /** @var LangRepository $partialMock */
        $partialMock = $this->getMockBuilder(LangRepository::class)
            ->setMethods(['findOneBy'])
            ->setConstructorArgs([$entityManagerMock, $classMetadataMock])
            ->getMock()
        ;

        $consecutiveCalls = 6;
        $partialMock
            ->expects($this->exactly($consecutiveCalls))
            ->method('findOneBy')
            ->willReturn(null)
        ;

        for ($i = 0; $i < $consecutiveCalls; ++$i) {
            if ($i % 2 == 0) {
                $locale = $partialMock->getOneByIsoCode('en');
            } else {
                $locale = $partialMock->getOneByLocale('en');
            }

            $this->assertNull($locale);
        }
    }

    /**
     * @param LanguageInterface|null $language
     *
     * @return MockObject|LangRepository
     */

    /**
     * @param array $expectedCriteria
     * @param LanguageInterface $language
     *
     * @return MockObject
     */
    private function buildPartialMock(array $expectedCriteria, LanguageInterface $language)
    {
        $entityManagerMock = $this->getMockBuilder(EntityManager::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $classMetadataMock = $this->getMockBuilder(ClassMetadata::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $partialMock = $this->getMockBuilder(LangRepository::class)
            ->setMethods(['findOneBy'])
            ->setConstructorArgs([$entityManagerMock, $classMetadataMock])
            ->getMock()
        ;

        $partialMock
            ->expects($this->once())
            ->method('findOneBy')
            ->with(
                $this->equalTo($expectedCriteria)
            )
            ->willReturn($language)
        ;

        return $partialMock;
    }

    /**
     * @return MockObject|LanguageInterface
     */
    private function buildLanguageMock()
    {
        $languageMock = $this->getMockBuilder(LanguageInterface::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $languageMock
            ->method('getLocale')
            ->willReturn('en-US')
        ;
        $languageMock
            ->method('getIsoCode')
            ->willReturn('en')
        ;

        return $languageMock;
    }
}
