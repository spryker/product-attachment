<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerTest\Zed\ProductAttachment\Communication\Plugin\ProductManagement;

use Codeception\Test\Unit;
use Generated\Shared\Transfer\LocaleTransfer;
use Generated\Shared\Transfer\ProductAbstractTransfer;
use Generated\Shared\Transfer\ProductAttachmentTransfer;
use Spryker\Zed\Locale\Business\LocaleFacadeInterface;
use Spryker\Zed\ProductAttachment\Communication\Form\Product\AttachmentForm;
use Spryker\Zed\ProductAttachment\Communication\Plugin\ProductManagement\ProductAttachmentProductAbstractTransferMapperPlugin;
use Spryker\Zed\ProductAttachment\ProductAttachmentConfig;
use Spryker\Zed\ProductAttachment\ProductAttachmentDependencyProvider;
use SprykerTest\Zed\ProductAttachment\ProductAttachmentCommunicationTester;

/**
 * Auto-generated group annotations
 *
 * @group SprykerTest
 * @group Zed
 * @group ProductAttachment
 * @group Communication
 * @group Plugin
 * @group ProductManagement
 * @group ProductAttachmentProductAbstractTransferMapperPluginTest
 * Add your own group annotations below this line
 */
class ProductAttachmentProductAbstractTransferMapperPluginTest extends Unit
{
    protected const string LOCALE_SECTION_DEFAULT = 'default';

    protected const string LOCALE_NAME_DE = 'de_DE';

    protected const string LOCALE_NAME_EN = 'en_US';

    protected const int ID_LOCALE_DE = 46;

    protected const int ID_LOCALE_EN = 66;

    protected const string FIELD_UNKNOWN = 'unknown_extension_field';

    protected ProductAttachmentCommunicationTester $tester;

    protected function setUp(): void
    {
        parent::setUp();

        $localeFacadeMock = $this->createMock(LocaleFacadeInterface::class);
        $localeFacadeMock->method('getLocaleCollection')->willReturn([
            static::LOCALE_NAME_DE => (new LocaleTransfer())->setIdLocale(static::ID_LOCALE_DE)->setLocaleName(static::LOCALE_NAME_DE),
            static::LOCALE_NAME_EN => (new LocaleTransfer())->setIdLocale(static::ID_LOCALE_EN)->setLocaleName(static::LOCALE_NAME_EN),
        ]);

        $this->tester->setDependency(ProductAttachmentDependencyProvider::FACADE_LOCALE, $localeFacadeMock);
    }

    public function testGivenAttachmentsInTheDefaultAndTheGermanSectionWhenMapThenEachAttachmentCarriesItsSectionLocale(): void
    {
        // Arrange
        $formData = [
            $this->getSectionFormName(static::LOCALE_SECTION_DEFAULT) => [
                $this->createAttachmentFormData('Installation guide', 'https://example.com/installation-guide.pdf', 1),
            ],
            $this->getSectionFormName(static::LOCALE_NAME_DE) => [
                $this->createAttachmentFormData('Installationsanleitung', 'https://example.com/installationsanleitung.pdf', 0),
            ],
        ];

        // Act
        $productAbstractTransfer = (new ProductAttachmentProductAbstractTransferMapperPlugin())->map($formData, new ProductAbstractTransfer());

        // Assert
        $this->assertSame(
            [
                [null, 'Installation guide', 'https://example.com/installation-guide.pdf', 1],
                [static::ID_LOCALE_DE, 'Installationsanleitung', 'https://example.com/installationsanleitung.pdf', 0],
            ],
            $this->extractAttachments($productAbstractTransfer),
        );
    }

    public function testGivenSeveralAttachmentsInTheDefaultSectionWhenMapThenAllAreMappedInFormOrder(): void
    {
        // Arrange
        $formData = [
            $this->getSectionFormName(static::LOCALE_SECTION_DEFAULT) => [
                $this->createAttachmentFormData('Warranty information', 'https://example.com/warranty.pdf', 0),
                $this->createAttachmentFormData('Safety guidelines', 'https://example.com/safety.pdf', 1),
                $this->createAttachmentFormData('Technical specifications', 'https://example.com/specifications.pdf', 2),
            ],
        ];

        // Act
        $productAbstractTransfer = (new ProductAttachmentProductAbstractTransferMapperPlugin())->map($formData, new ProductAbstractTransfer());

        // Assert
        $this->assertSame(
            [
                [null, 'Warranty information', 'https://example.com/warranty.pdf', 0],
                [null, 'Safety guidelines', 'https://example.com/safety.pdf', 1],
                [null, 'Technical specifications', 'https://example.com/specifications.pdf', 2],
            ],
            $this->extractAttachments($productAbstractTransfer),
        );
    }

    public function testGivenEveryAttachmentWasRemovedFromTheFormWhenMapThenTheProductAbstractHasNoAttachments(): void
    {
        // Arrange
        $formData = [
            $this->getSectionFormName(static::LOCALE_SECTION_DEFAULT) => [],
        ];
        $productAbstractTransfer = (new ProductAbstractTransfer())->addProductAttachment(
            (new ProductAttachmentTransfer())->setLabel('Temporary document')->setUrl('https://example.com/temporary.pdf'),
        );

        // Act
        $productAbstractTransfer = (new ProductAttachmentProductAbstractTransferMapperPlugin())->map($formData, $productAbstractTransfer);

        // Assert
        $this->assertCount(0, $productAbstractTransfer->getProductAttachments());
    }

    public function testGivenOneAttachmentPerLocaleSectionWhenMapThenEachSectionIsMappedToItsOwnLocale(): void
    {
        // Arrange
        $formData = [
            $this->getSectionFormName(static::LOCALE_SECTION_DEFAULT) => [
                $this->createAttachmentFormData('Default manual', 'https://example.com/default-manual.pdf', 0),
            ],
            $this->getSectionFormName(static::LOCALE_NAME_DE) => [
                $this->createAttachmentFormData('German manual', 'https://example.com/german-manual.pdf', 0),
            ],
            $this->getSectionFormName(static::LOCALE_NAME_EN) => [
                $this->createAttachmentFormData('English manual', 'https://example.com/english-manual.pdf', 0),
            ],
        ];

        // Act
        $productAbstractTransfer = (new ProductAttachmentProductAbstractTransferMapperPlugin())->map($formData, new ProductAbstractTransfer());

        // Assert
        $this->assertSame(
            [
                [null, 'Default manual', 'https://example.com/default-manual.pdf', 0],
                [static::ID_LOCALE_DE, 'German manual', 'https://example.com/german-manual.pdf', 0],
                [static::ID_LOCALE_EN, 'English manual', 'https://example.com/english-manual.pdf', 0],
            ],
            $this->extractAttachments($productAbstractTransfer),
        );
    }

    public function testGivenRowsWithExtensionKeysWhenMapThenCoreValuesAreKeptAndUnknownKeysAreIgnored(): void
    {
        // Arrange
        $formData = [
            $this->getSectionFormName(static::LOCALE_NAME_DE) => [
                $this->createAttachmentFormData('Handbuch', 'https://example.com/handbuch.pdf', 3) + [static::FIELD_UNKNOWN => 'value', AttachmentForm::FIELD_FK_LOCALE => static::ID_LOCALE_EN],
                $this->createAttachmentFormData('', 'https://example.com/without-label.pdf', 4) + [static::FIELD_UNKNOWN => 'value'],
            ],
        ];

        // Act
        $productAbstractTransfer = (new ProductAttachmentProductAbstractTransferMapperPlugin())->map($formData, new ProductAbstractTransfer());

        // Assert
        $this->assertSame([[static::ID_LOCALE_DE, 'Handbuch', 'https://example.com/handbuch.pdf', 3]], $this->extractAttachments($productAbstractTransfer));
    }

    protected function getSectionFormName(string $localeName): string
    {
        return sprintf('%s_%s', (new ProductAttachmentConfig())->getAttachmentFormName(), $localeName);
    }

    /**
     * @return array<string, string|int>
     */
    protected function createAttachmentFormData(string $label, string $url, int $sortOrder): array
    {
        return [
            AttachmentForm::FIELD_LABEL => $label,
            AttachmentForm::FIELD_URL => $url,
            AttachmentForm::FIELD_SORT_ORDER => $sortOrder,
        ];
    }

    /**
     * @return list<array{int|null, string|null, string|null, int|null}>
     */
    protected function extractAttachments(ProductAbstractTransfer $productAbstractTransfer): array
    {
        return array_values(array_map(
            static fn (ProductAttachmentTransfer $productAttachmentTransfer): array => [
                $productAttachmentTransfer->getFkLocale(),
                $productAttachmentTransfer->getLabel(),
                $productAttachmentTransfer->getUrl(),
                $productAttachmentTransfer->getSortOrder(),
            ],
            $productAbstractTransfer->getProductAttachments()->getArrayCopy(),
        ));
    }
}
