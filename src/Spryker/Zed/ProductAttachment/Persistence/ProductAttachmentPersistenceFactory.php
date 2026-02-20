<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\ProductAttachment\Persistence;

use Orm\Zed\ProductAttachment\Persistence\SpyProductAttachmentProductAbstractQuery;
use Orm\Zed\ProductAttachment\Persistence\SpyProductAttachmentQuery;
use Spryker\Zed\Kernel\Persistence\AbstractPersistenceFactory;
use Spryker\Zed\ProductAttachment\Persistence\Mapper\ProductAttachmentMapper;

/**
 * @method \Spryker\Zed\ProductAttachment\Persistence\ProductAttachmentEntityManagerInterface getEntityManager()
 * @method \Spryker\Zed\ProductAttachment\Persistence\ProductAttachmentRepositoryInterface getRepository()
 * @method \Spryker\Zed\ProductAttachment\ProductAttachmentConfig getConfig()
 */
class ProductAttachmentPersistenceFactory extends AbstractPersistenceFactory
{
    public function createProductAttachmentMapper(): ProductAttachmentMapper
    {
        return new ProductAttachmentMapper();
    }

    public function createProductAttachmentQuery(): SpyProductAttachmentQuery
    {
        return SpyProductAttachmentQuery::create();
    }

    public function createProductAttachmentProductAbstractQuery(): SpyProductAttachmentProductAbstractQuery
    {
        return SpyProductAttachmentProductAbstractQuery::create();
    }
}
