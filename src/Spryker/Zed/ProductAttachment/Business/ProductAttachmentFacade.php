<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\ProductAttachment\Business;

use Generated\Shared\Transfer\ProductAbstractTransfer;
use Generated\Shared\Transfer\ProductAttachmentCollectionTransfer;
use Generated\Shared\Transfer\ProductAttachmentCriteriaTransfer;
use Spryker\Zed\Kernel\Business\AbstractFacade;

/**
 * {@inheritDoc}
 *
 * @api
 *
 * @method \Spryker\Zed\ProductAttachment\Business\ProductAttachmentBusinessFactory getFactory()
 * @method \Spryker\Zed\ProductAttachment\Persistence\ProductAttachmentEntityManagerInterface getEntityManager()
 * @method \Spryker\Zed\ProductAttachment\Persistence\ProductAttachmentRepositoryInterface getRepository()
 */
class ProductAttachmentFacade extends AbstractFacade implements ProductAttachmentFacadeInterface
{
    public function getProductAttachmentCollection(
        ProductAttachmentCriteriaTransfer $productAttachmentCriteriaTransfer,
    ): ProductAttachmentCollectionTransfer {
        return $this->getRepository()->getProductAttachmentCollection($productAttachmentCriteriaTransfer);
    }

    public function saveProductAbstractAttachmentCollection(ProductAbstractTransfer $productAbstractTransfer): void
    {
        $this->getFactory()
            ->createProductAbstractAttachmentCollectionWriter()
            ->saveProductAbstractAttachments($productAbstractTransfer);
    }
}
