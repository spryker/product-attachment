<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\ProductAttachment\Business\Reader;

use Generated\Shared\Transfer\ProductAttachmentCollectionTransfer;
use Generated\Shared\Transfer\ProductAttachmentCriteriaTransfer;
use Spryker\Zed\ProductAttachment\Persistence\ProductAttachmentRepositoryInterface;

class ProductAttachmentReader implements ProductAttachmentReaderInterface
{
    /**
     * @param \Spryker\Zed\ProductAttachment\Persistence\ProductAttachmentRepositoryInterface $repository
     * @param list<\Spryker\Zed\ProductAttachmentExtension\Dependency\Plugin\ProductAttachmentCollectionExpanderPluginInterface> $productAttachmentCollectionExpanderPlugins
     */
    public function __construct(
        protected ProductAttachmentRepositoryInterface $repository,
        protected array $productAttachmentCollectionExpanderPlugins,
    ) {
    }

    public function getProductAttachmentCollection(
        ProductAttachmentCriteriaTransfer $productAttachmentCriteriaTransfer,
    ): ProductAttachmentCollectionTransfer {
        $productAttachmentCollectionTransfer = $this->repository->getProductAttachmentCollection($productAttachmentCriteriaTransfer);

        return $this->executeProductAttachmentCollectionExpanderPlugins($productAttachmentCollectionTransfer);
    }

    protected function executeProductAttachmentCollectionExpanderPlugins(
        ProductAttachmentCollectionTransfer $productAttachmentCollectionTransfer,
    ): ProductAttachmentCollectionTransfer {
        foreach ($this->productAttachmentCollectionExpanderPlugins as $productAttachmentCollectionExpanderPlugin) {
            $productAttachmentCollectionTransfer = $productAttachmentCollectionExpanderPlugin->expand($productAttachmentCollectionTransfer);
        }

        return $productAttachmentCollectionTransfer;
    }
}
