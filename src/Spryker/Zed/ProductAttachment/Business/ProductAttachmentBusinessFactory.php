<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\ProductAttachment\Business;

use Generated\Shared\Transfer\DataImporterConfigurationTransfer;
use Generated\Shared\Transfer\DataImporterDataSourceConfigurationTransfer;
use Orm\Zed\Locale\Persistence\SpyLocaleQuery;
use Orm\Zed\Product\Persistence\SpyProductAbstractQuery;
use Orm\Zed\ProductAttachment\Persistence\SpyProductAttachmentProductAbstractQuery;
use Orm\Zed\ProductAttachment\Persistence\SpyProductAttachmentQuery;
use Spryker\Zed\DataImport\Business\DataImportFactoryTrait;
use Spryker\Zed\DataImport\Business\Model\DataImporterInterface;
use Spryker\Zed\DataImport\Business\Model\DataImportStep\DataImportStepInterface;
use Spryker\Zed\Kernel\Business\AbstractBusinessFactory;
use Spryker\Zed\ProductAttachment\Business\Attachment\AttachmentCreator;
use Spryker\Zed\ProductAttachment\Business\Attachment\AttachmentCreatorInterface;
use Spryker\Zed\ProductAttachment\Business\Attachment\AttachmentUpdater;
use Spryker\Zed\ProductAttachment\Business\Attachment\AttachmentUpdaterInterface;
use Spryker\Zed\ProductAttachment\Business\DataImport\Step\LocaleNameToIdStep;
use Spryker\Zed\ProductAttachment\Business\DataImport\Step\ProductAbstractSkuToIdStep;
use Spryker\Zed\ProductAttachment\Business\DataImport\Step\ProductAttachmentWriterStep;
use Spryker\Zed\ProductAttachment\Business\Writer\ProductAbstractAttachmentCollectionWriter;
use Spryker\Zed\ProductAttachment\Business\Writer\ProductAbstractAttachmentCollectionWriterInterface;
use Spryker\Zed\ProductAttachment\ProductAttachmentDependencyProvider;

/**
 * @method \Spryker\Zed\ProductAttachment\Persistence\ProductAttachmentEntityManagerInterface getEntityManager()
 * @method \Spryker\Zed\ProductAttachment\Persistence\ProductAttachmentRepositoryInterface getRepository()
 * @method \Spryker\Zed\ProductAttachment\ProductAttachmentConfig getConfig()
 */
class ProductAttachmentBusinessFactory extends AbstractBusinessFactory
{
    use DataImportFactoryTrait;

    public function createAttachmentCreator(): AttachmentCreatorInterface
    {
        return new AttachmentCreator(
            $this->getEntityManager(),
        );
    }

    public function createAttachmentUpdater(): AttachmentUpdaterInterface
    {
        return new AttachmentUpdater(
            $this->getEntityManager(),
        );
    }

    public function createProductAbstractAttachmentCollectionWriter(): ProductAbstractAttachmentCollectionWriterInterface
    {
        return new ProductAbstractAttachmentCollectionWriter(
            $this->getRepository(),
            $this->getEntityManager(),
            $this->createAttachmentCreator(),
            $this->createAttachmentUpdater(),
        );
    }

    public function getProductAttachmentDataImporter(?DataImporterConfigurationTransfer $dataImporterConfigurationTransfer = null): DataImporterInterface
    {
        /** @var \Spryker\Zed\DataImport\Business\Model\DataImporter $dataImporter */
        $dataImporter = $this->getDataImporter(
            $this->getConfig()->getProductAttachmentDataImporterConfiguration(),
            $dataImporterConfigurationTransfer,
        );

        /** @var \Spryker\Zed\DataImport\Business\Model\DataSet\DataSetStepBroker $dataSetStepBroker */
        $dataSetStepBroker = $this->createTransactionAwareDataSetStepBroker($this->getConfig()->getProductAttachmentDataImporterBulkSize());
        $dataSetStepBroker
            ->addStep($this->createProductAbstractSkuToIdStep())
            ->addStep($this->createLocaleNameToIdStep())
            ->addStep($this->createProductAttachmentWriterStep());

        $dataImporter->addDataSetStepBroker($dataSetStepBroker);

        return $dataImporter;
    }

    public function getDataImporter(
        DataImporterDataSourceConfigurationTransfer $dataImporterDataSourceConfigurationTransfer,
        ?DataImporterConfigurationTransfer $dataImporterConfigurationTransfer = null
    ): DataImporterInterface {
        if ($dataImporterConfigurationTransfer) {
            return $this->getDataImportFactory()->getCsvDataImporterFromConfig($dataImporterConfigurationTransfer);
        }

        return $this->getCsvDataImporterFromConfig(
            $dataImporterDataSourceConfigurationTransfer,
        );
    }

    public function createProductAbstractSkuToIdStep(): DataImportStepInterface
    {
        return new ProductAbstractSkuToIdStep(
            $this->getProductAbstractQuery(),
            $this->getConfig(),
        );
    }

    public function createLocaleNameToIdStep(): DataImportStepInterface
    {
        return new LocaleNameToIdStep(
            $this->getLocaleQuery(),
        );
    }

    public function createProductAttachmentWriterStep(): DataImportStepInterface
    {
        return new ProductAttachmentWriterStep(
            $this->getProductAttachmentQuery(),
            $this->getProductAttachmentProductAbstractQuery(),
        );
    }

    public function getProductAbstractQuery(): SpyProductAbstractQuery
    {
        return $this->getProvidedDependency(ProductAttachmentDependencyProvider::PROPEL_QUERY_PRODUCT_ABSTRACT);
    }

    public function getLocaleQuery(): SpyLocaleQuery
    {
        return $this->getProvidedDependency(ProductAttachmentDependencyProvider::PROPEL_QUERY_LOCALE);
    }

    public function getProductAttachmentQuery(): SpyProductAttachmentQuery
    {
        return SpyProductAttachmentQuery::create();
    }

    public function getProductAttachmentProductAbstractQuery(): SpyProductAttachmentProductAbstractQuery
    {
        return SpyProductAttachmentProductAbstractQuery::create();
    }
}
