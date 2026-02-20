<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\ProductAttachment\Business\DataImport\DataSet;

interface ProductAttachmentDataSetInterface
{
    public const string COLUMN_ABSTRACT_SKU = 'abstract_sku';

    public const string COLUMN_LABEL = 'label';

    public const string COLUMN_LOCALE = 'locale';

    public const string COLUMN_ATTACHMENT_URL = 'attachment_url';

    public const string COLUMN_SORT_ORDER = 'sort_order';

    public const string KEY_ID_PRODUCT_ABSTRACT = 'id_product_abstract';

    public const string KEY_ID_LOCALE = 'id_locale';
}
