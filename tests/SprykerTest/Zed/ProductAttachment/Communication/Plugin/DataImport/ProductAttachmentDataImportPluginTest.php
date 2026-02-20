<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerTest\Zed\ProductAttachment\Communication\Plugin\DataImport;

use Codeception\Test\Unit;
use Generated\Shared\Transfer\DataImporterConfigurationTransfer;
use Generated\Shared\Transfer\DataImporterReaderConfigurationTransfer;
use Generated\Shared\Transfer\DataImporterReportTransfer;
use Spryker\Zed\DataImport\Business\Exception\DataImportException;
use Spryker\Zed\ProductAttachment\Communication\Plugin\DataImport\ProductAttachmentDataImportPlugin;
use SprykerTest\Zed\ProductAttachment\ProductAttachmentCommunicationTester;

/**
 * Auto-generated group annotations
 *
 * @group SprykerTest
 * @group Zed
 * @group ProductAttachment
 * @group Communication
 * @group Plugin
 * @group DataImport
 * @group ProductAttachmentDataImportPluginTest
 * Add your own group annotations below this line
 */
class ProductAttachmentDataImportPluginTest extends Unit
{
    protected const int EXPECTED_IMPORT_COUNT = 4;

    protected const string IMPORT_FILE_PATH = 'import/product_attachment.csv';

    protected const string IMPORT_FILE_PATH_INVALID = 'import/product_attachment_invalid.csv';

    protected const string IMPORT_FILE_PATH_INVALID_URL = 'import/product_attachment_invalid_url.csv';

    protected const string IMPORT_FILE_PATH_EMPTY_LOCALE = 'import/product_attachment_empty_locale.csv';

    protected const string IMPORT_FILE_PATH_DEFAULT_LOCALE = 'import/product_attachment_default_locale.csv';

    protected ProductAttachmentCommunicationTester $tester;

    protected function setUp(): void
    {
        parent::setUp();

        $this->tester->ensureProductAttachmentTablesAreEmpty();
    }

    public function testGivenValidCsvDataWhenImportingProductAttachmentsThenProductAttachmentsAreCreated(): void
    {
        // Arrange
        $this->tester->haveProductAbstract(['sku' => 'test_001']);
        $this->tester->haveProductAbstract(['sku' => 'test_002']);
        $this->tester->haveLocale(['locale_name' => 'en_US']);
        $this->tester->haveLocale(['locale_name' => 'de_DE']);

        $configurationTransfer = new DataImporterReaderConfigurationTransfer();
        $configurationTransfer->setFileName(codecept_data_dir() . static::IMPORT_FILE_PATH);

        $dataImporterConfigurationTransfer = (new DataImporterConfigurationTransfer())
            ->setReaderConfiguration($configurationTransfer);

        $productAttachmentDataImportPlugin = new ProductAttachmentDataImportPlugin();

        // Act
        $dataImporterReportTransfer = $productAttachmentDataImportPlugin->import($dataImporterConfigurationTransfer);

        // Assert
        $this->assertInstanceOf(DataImporterReportTransfer::class, $dataImporterReportTransfer);
        $this->assertTrue($dataImporterReportTransfer->getIsSuccess());
        $this->assertSame(static::EXPECTED_IMPORT_COUNT, $dataImporterReportTransfer->getImportedDataSetCount());

        $attachments = $this->tester->getAllProductAttachments();
        $this->assertCount(static::EXPECTED_IMPORT_COUNT, $attachments);

        $attachmentLabels = array_map(
            fn ($attachment) => $attachment->getLabel(),
            $attachments,
        );

        $this->assertContains('User Manual', $attachmentLabels);
        $this->assertContains('Datasheet', $attachmentLabels);
        $this->assertContains('Installation Guide', $attachmentLabels);

        $userManualAttachment = $this->tester->findProductAttachmentByLabel('User Manual');
        $this->assertNotNull($userManualAttachment);
        $this->assertSame('https://example.com/manual-test_001.pdf', $userManualAttachment->getUrl());

        $relations = $this->tester->getAllProductAttachmentProductAbstractRelations();
        $this->assertCount(static::EXPECTED_IMPORT_COUNT, $relations);
    }

    public function testGivenInvalidCsvDataWithMissingLabelWhenImportingProductAttachmentsThenExceptionIsThrown(): void
    {
        // Arrange
        $this->tester->haveProductAbstract(['sku' => 'test_003']);
        $this->tester->haveLocale(['locale_name' => 'en_US']);

        $configurationTransfer = new DataImporterReaderConfigurationTransfer();
        $configurationTransfer->setFileName(codecept_data_dir() . static::IMPORT_FILE_PATH_INVALID);

        $dataImporterConfigurationTransfer = (new DataImporterConfigurationTransfer())
            ->setReaderConfiguration($configurationTransfer)
            ->setThrowException(true);

        // Assert
        $this->expectException(DataImportException::class);

        // Act
        $productAttachmentDataImportPlugin = new ProductAttachmentDataImportPlugin();
        $productAttachmentDataImportPlugin->import($dataImporterConfigurationTransfer);
    }

    public function testGivenInvalidCsvDataWithMissingUrlWhenImportingProductAttachmentsThenExceptionIsThrown(): void
    {
        // Arrange
        $this->tester->haveProductAbstract(['sku' => 'test_004']);
        $this->tester->haveLocale(['locale_name' => 'en_US']);

        $configurationTransfer = new DataImporterReaderConfigurationTransfer();
        $configurationTransfer->setFileName(codecept_data_dir() . static::IMPORT_FILE_PATH_INVALID_URL);

        $dataImporterConfigurationTransfer = (new DataImporterConfigurationTransfer())
            ->setReaderConfiguration($configurationTransfer)
            ->setThrowException(true);

        // Assert
        $this->expectException(DataImportException::class);
        $this->expectExceptionMessage('Url is required.');

        // Act
        $productAttachmentDataImportPlugin = new ProductAttachmentDataImportPlugin();
        $productAttachmentDataImportPlugin->import($dataImporterConfigurationTransfer);
    }

    public function testGivenPluginWhenGettingImportTypeThenCorrectImportTypeIsReturned(): void
    {
        // Arrange
        $productAttachmentDataImportPlugin = new ProductAttachmentDataImportPlugin();

        // Act
        $importType = $productAttachmentDataImportPlugin->getImportType();

        // Assert
        $this->assertSame('product-attachment', $importType);
    }

    public function testGivenCsvDataWithEmptyLocaleWhenImportingProductAttachmentsThenAttachmentIsCreatedWithNullLocale(): void
    {
        // Arrange
        $this->tester->haveProductAbstract(['sku' => 'test_005']);

        $configurationTransfer = new DataImporterReaderConfigurationTransfer();
        $configurationTransfer->setFileName(codecept_data_dir() . static::IMPORT_FILE_PATH_EMPTY_LOCALE);

        $dataImporterConfigurationTransfer = (new DataImporterConfigurationTransfer())
            ->setReaderConfiguration($configurationTransfer);

        $productAttachmentDataImportPlugin = new ProductAttachmentDataImportPlugin();

        // Act
        $dataImporterReportTransfer = $productAttachmentDataImportPlugin->import($dataImporterConfigurationTransfer);

        // Assert
        $this->assertInstanceOf(DataImporterReportTransfer::class, $dataImporterReportTransfer);
        $this->assertTrue($dataImporterReportTransfer->getIsSuccess());
        $this->assertSame(1, $dataImporterReportTransfer->getImportedDataSetCount());

        $attachment = $this->tester->findProductAttachmentByLabel('Empty Locale Test');
        $this->assertNotNull($attachment);
        $this->assertNull($attachment->getFkLocale());
    }

    public function testGivenCsvDataWithDefaultLocaleWhenImportingProductAttachmentsThenAttachmentIsCreatedWithNullLocale(): void
    {
        // Arrange
        $this->tester->haveProductAbstract(['sku' => 'test_006']);

        $configurationTransfer = new DataImporterReaderConfigurationTransfer();
        $configurationTransfer->setFileName(codecept_data_dir() . static::IMPORT_FILE_PATH_DEFAULT_LOCALE);

        $dataImporterConfigurationTransfer = (new DataImporterConfigurationTransfer())
            ->setReaderConfiguration($configurationTransfer);

        $productAttachmentDataImportPlugin = new ProductAttachmentDataImportPlugin();

        // Act
        $dataImporterReportTransfer = $productAttachmentDataImportPlugin->import($dataImporterConfigurationTransfer);

        // Assert
        $this->assertInstanceOf(DataImporterReportTransfer::class, $dataImporterReportTransfer);
        $this->assertTrue($dataImporterReportTransfer->getIsSuccess());
        $this->assertSame(1, $dataImporterReportTransfer->getImportedDataSetCount());

        $attachment = $this->tester->findProductAttachmentByLabel('Default Locale Test');
        $this->assertNotNull($attachment);
        $this->assertNull($attachment->getFkLocale());
    }

    protected function _after(): void
    {
        parent::_after();

        $this->tester->ensureProductAttachmentTablesAreEmpty();
    }
}
