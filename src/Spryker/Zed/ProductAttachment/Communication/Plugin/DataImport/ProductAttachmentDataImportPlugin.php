<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\ProductAttachment\Communication\Plugin\DataImport;

use Generated\Shared\Transfer\DataImporterConfigurationTransfer;
use Generated\Shared\Transfer\DataImporterReportTransfer;
use Spryker\Zed\DataImport\Dependency\Plugin\DataImportPluginInterface;
use Spryker\Zed\Kernel\Communication\AbstractPlugin;
use Spryker\Zed\ProductAttachment\ProductAttachmentConfig;

/**
 * @method \Spryker\Zed\ProductAttachment\Business\ProductAttachmentFacadeInterface getFacade()
 * @method \Spryker\Zed\ProductAttachment\ProductAttachmentConfig getConfig()
 * @method \Spryker\Zed\ProductAttachment\Business\ProductAttachmentBusinessFactory getBusinessFactory()
 */
class ProductAttachmentDataImportPlugin extends AbstractPlugin implements DataImportPluginInterface
{
    /**
     * {@inheritDoc}
     * - Imports product attachments from CSV file.
     * - Creates or updates product attachments based on abstract SKU, label, locale, URL, and sort order.
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\DataImporterConfigurationTransfer|null $dataImporterConfigurationTransfer
     *
     * @return \Generated\Shared\Transfer\DataImporterReportTransfer
     */
    public function import(?DataImporterConfigurationTransfer $dataImporterConfigurationTransfer = null): DataImporterReportTransfer
    {
        return $this->getBusinessFactory()->getProductAttachmentDataImporter($dataImporterConfigurationTransfer)->import($dataImporterConfigurationTransfer);
    }

    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @return string
     */
    public function getImportType(): string
    {
        return ProductAttachmentConfig::IMPORT_TYPE_PRODUCT_ATTACHMENT;
    }
}
