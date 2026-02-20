<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\ProductAttachment\Business\Attachment;

use Generated\Shared\Transfer\ProductAttachmentTransfer;

interface AttachmentCreatorInterface
{
    public function createAttachment(ProductAttachmentTransfer $productAttachmentTransfer): ProductAttachmentTransfer;
}
