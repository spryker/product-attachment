<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\ProductAttachment;

use Generated\Shared\Transfer\DataImporterDataSourceConfigurationTransfer;
use Spryker\Zed\Kernel\AbstractBundleConfig;

class ProductAttachmentConfig extends AbstractBundleConfig
{
    public const string IMPORT_TYPE_PRODUCT_ATTACHMENT = 'product-attachment';

    public const int IMPORT_BULK_SIZE = 1000;

    protected const string MODULE_NAME = 'ProductAttachment';

    protected const string IMPORT_FILE_NAME = 'product_attachment.csv';

    /**
     * Specification:
     * - Import configuration for product attachment.
     *
     * @api
     *
     * @return \Generated\Shared\Transfer\DataImporterDataSourceConfigurationTransfer
     */
    public function getProductAttachmentDataImporterConfiguration(): DataImporterDataSourceConfigurationTransfer
    {
        return (new DataImporterDataSourceConfigurationTransfer())
            ->setImportType(static::IMPORT_TYPE_PRODUCT_ATTACHMENT)
            ->setFileName(static::IMPORT_FILE_NAME)
            ->setModuleName(static::MODULE_NAME)
            ->setDirectory('/data/import/common/common/');
    }

    /**
     * Specification:
     * - Bulk size for product attachment import.
     *
     * @api
     *
     * @return int
     */
    public function getProductAttachmentDataImporterBulkSize(): int
    {
        return static::IMPORT_BULK_SIZE;
    }

    /**
     * Specification:
     * - Returns the name of the attachment form.
     *
     * @api
     */
    public function getAttachmentFormName(): string
    {
        return 'attachment';
    }

    /**
     * Specification:
     * - Returns the default locale for product attachment.
     *
     * @api
     */
    public function getProductAttachmentDefaultLocale(): string
    {
        return 'default';
    }
}
