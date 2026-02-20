<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\ProductAttachment\Business\Writer;

use Generated\Shared\Transfer\ProductAbstractTransfer;

interface ProductAbstractAttachmentCollectionWriterInterface
{
    public function saveProductAbstractAttachments(ProductAbstractTransfer $productAbstractTransfer): void;
}
