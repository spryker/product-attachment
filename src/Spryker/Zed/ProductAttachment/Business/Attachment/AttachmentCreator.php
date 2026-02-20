<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\ProductAttachment\Business\Attachment;

use Generated\Shared\Transfer\ProductAttachmentTransfer;
use Spryker\Zed\Kernel\Persistence\EntityManager\TransactionTrait;
use Spryker\Zed\ProductAttachment\Persistence\ProductAttachmentEntityManagerInterface;

class AttachmentCreator implements AttachmentCreatorInterface
{
    use TransactionTrait;

    public function __construct(
        protected ProductAttachmentEntityManagerInterface $entityManager,
    ) {
    }

    public function createAttachment(ProductAttachmentTransfer $productAttachmentTransfer): ProductAttachmentTransfer
    {
        return $this->getTransactionHandler()->handleTransaction(function () use ($productAttachmentTransfer) {
            return $this->executeCreateAttachmentTransaction($productAttachmentTransfer);
        });
    }

    protected function executeCreateAttachmentTransaction(
        ProductAttachmentTransfer $productAttachmentTransfer,
    ): ProductAttachmentTransfer {
        return $this->entityManager->createProductAttachment($productAttachmentTransfer);
    }
}
