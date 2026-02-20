<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\ProductAttachment\Business\DataImport\Step;

use Orm\Zed\ProductAttachment\Persistence\SpyProductAttachment;
use Orm\Zed\ProductAttachment\Persistence\SpyProductAttachmentProductAbstractQuery;
use Orm\Zed\ProductAttachment\Persistence\SpyProductAttachmentQuery;
use Spryker\Zed\DataImport\Business\Exception\EntityNotFoundException;
use Spryker\Zed\DataImport\Business\Model\DataImportStep\DataImportStepInterface;
use Spryker\Zed\DataImport\Business\Model\DataSet\DataSetInterface;
use Spryker\Zed\ProductAttachment\Business\DataImport\DataSet\ProductAttachmentDataSetInterface;

class ProductAttachmentWriterStep implements DataImportStepInterface
{
    public function __construct(
        protected SpyProductAttachmentQuery $productAttachmentQuery,
        protected SpyProductAttachmentProductAbstractQuery $productAttachmentProductAbstractQuery,
    ) {
    }

    public function execute(DataSetInterface $dataSet): void
    {
        $idProductAbstract = $dataSet[ProductAttachmentDataSetInterface::KEY_ID_PRODUCT_ABSTRACT];
        $sortOrder = (int)$dataSet[ProductAttachmentDataSetInterface::COLUMN_SORT_ORDER];

        $productAttachmentEntity = $this->findOrCreateProductAttachment(
            $idProductAbstract,
            $dataSet,
        );

        if ($productAttachmentEntity->isNew() || $productAttachmentEntity->isModified()) {
            $productAttachmentEntity->save();
        }

        $this->createProductAttachmentRelation(
            $productAttachmentEntity->getIdProductAttachment(),
            $idProductAbstract,
            $sortOrder,
        );
    }

    protected function findOrCreateProductAttachment(
        int $idProductAbstract,
        DataSetInterface $dataSet,
    ): SpyProductAttachment {
        $idLocale = $dataSet[ProductAttachmentDataSetInterface::KEY_ID_LOCALE];
        $label = (string)$dataSet[ProductAttachmentDataSetInterface::COLUMN_LABEL];
        $url = (string)$dataSet[ProductAttachmentDataSetInterface::COLUMN_ATTACHMENT_URL];

        if (!$url) {
            throw new EntityNotFoundException('Url is required.');
        }

        if (!$label) {
            throw new EntityNotFoundException('Label is required.');
        }

        /**
         * @var \Orm\Zed\ProductAttachment\Persistence\SpyProductAttachment|null $productAttachmentEntity
         */
        $productAttachmentEntity = $this->productAttachmentQuery
            ->clear()
            ->filterByLabel($label)
            ->filterByFkLocale($idLocale)
            ->filterByUrl($url)
            ->useProductAttachmentProductAbstractQuery()
                ->filterByFkProductAbstract($idProductAbstract)
            ->endUse()
            ->findOne();

        if ($productAttachmentEntity !== null) {
            return $productAttachmentEntity;
        }

        return (new SpyProductAttachment())
            ->setLabel($label)
            ->setUrl($url)
            ->setFkLocale($idLocale);
    }

    protected function createProductAttachmentRelation(
        int $idProductAttachment,
        int $idProductAbstract,
        int $sortOrder,
    ): void {
        $relationEntity = $this->productAttachmentProductAbstractQuery
            ->clear()
            ->filterByFkProductAbstract($idProductAbstract)
            ->filterByFkProductAttachment($idProductAttachment)
            ->findOneOrCreate();

        $relationEntity->setOrder($sortOrder);

        if ($relationEntity->isNew() || $relationEntity->isModified()) {
            $relationEntity->save();
        }
    }
}
