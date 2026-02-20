<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\ProductAttachment\Business\DataImport\Step;

use Orm\Zed\Product\Persistence\SpyProductAbstractQuery;
use Spryker\Zed\DataImport\Business\Exception\EntityNotFoundException;
use Spryker\Zed\DataImport\Business\Model\DataImportStep\DataImportStepInterface;
use Spryker\Zed\DataImport\Business\Model\DataSet\DataSetInterface;
use Spryker\Zed\ProductAttachment\Business\DataImport\DataSet\ProductAttachmentDataSetInterface;
use Spryker\Zed\ProductAttachment\ProductAttachmentConfig;

class ProductAbstractSkuToIdStep implements DataImportStepInterface
{
    /**
     * @var array<string, int>
     */
    protected array $idProductAbstractCache = [];

    public function __construct(
        protected SpyProductAbstractQuery $productAbstractQuery,
        protected ProductAttachmentConfig $productAttachmentConfig,
    ) {
    }

    public function execute(DataSetInterface $dataSet): void
    {
        $abstractSku = $dataSet[ProductAttachmentDataSetInterface::COLUMN_ABSTRACT_SKU];

        if (isset($this->idProductAbstractCache[$abstractSku])) {
            $dataSet[ProductAttachmentDataSetInterface::KEY_ID_PRODUCT_ABSTRACT] = $this->idProductAbstractCache[$abstractSku];

            return;
        }

        $productAbstractEntity = $this->productAbstractQuery
            ->clear()
            ->filterBySku($abstractSku)
            ->findOne();

        if ($productAbstractEntity === null) {
            throw new EntityNotFoundException(sprintf(
                'Product abstract with SKU "%s" not found.',
                $abstractSku,
            ));
        }

        $this->clearCacheIfLimitReached();

        $idProductAbstract = $productAbstractEntity->getIdProductAbstract();
        $this->idProductAbstractCache[$abstractSku] = $idProductAbstract;
        $dataSet[ProductAttachmentDataSetInterface::KEY_ID_PRODUCT_ABSTRACT] = $idProductAbstract;
    }

    protected function clearCacheIfLimitReached(): void
    {
        if (count($this->idProductAbstractCache) >= $this->productAttachmentConfig->getProductAttachmentDataImporterBulkSize()) {
            $this->idProductAbstractCache = [];
        }
    }
}
