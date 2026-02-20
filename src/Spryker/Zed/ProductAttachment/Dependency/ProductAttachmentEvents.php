<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\ProductAttachment\Dependency;

interface ProductAttachmentEvents
{
    /**
     * Specification:
     * - This event will be used for spy_product_attachment entity creation.
     *
     * @api
     */
    public const string ENTITY_SPY_PRODUCT_ATTACHMENT_CREATE = 'Entity.spy_product_attachment.create';

    /**
     * Specification:
     * - This event will be used for spy_product_attachment entity update.
     *
     * @api
     */
    public const string ENTITY_SPY_PRODUCT_ATTACHMENT_UPDATE = 'Entity.spy_product_attachment.update';

    /**
     * Specification:
     * - This event will be used for spy_product_attachment entity deletion.
     *
     * @api
     */
    public const string ENTITY_SPY_PRODUCT_ATTACHMENT_DELETE = 'Entity.spy_product_attachment.delete';

    /**
     * Specification:
     * - This event will be used for spy_product_attachment_product_abstract entity creation.
     *
     * @api
     */
    public const string ENTITY_SPY_PRODUCT_ATTACHMENT_PRODUCT_ABSTRACT_CREATE = 'Entity.spy_product_attachment_product_abstract.create';

    /**
     * Specification:
     * - This event will be used for spy_product_attachment_product_abstract entity update.
     *
     * @api
     */
    public const string ENTITY_SPY_PRODUCT_ATTACHMENT_PRODUCT_ABSTRACT_UPDATE = 'Entity.spy_product_attachment_product_abstract.update';

    /**
     * Specification:
     * - This event will be used for spy_product_attachment_product_abstract entity deletion.
     *
     * @api
     */
    public const string ENTITY_SPY_PRODUCT_ATTACHMENT_PRODUCT_ABSTRACT_DELETE = 'Entity.spy_product_attachment_product_abstract.delete';

    /**
     * Specification:
     * - This event will be used for product_abstract_attachment publishing.
     *
     * @api
     */
    public const string PRODUCT_ABSTRACT_ATTACHMENT_PUBLISH = 'ProductAttachment.product_abstract_attachment.publish';

    /**
     * Specification:
     * - This event will be used for product_abstract_attachment unpublishing.
     *
     * @api
     */
    public const string PRODUCT_ABSTRACT_ATTACHMENT_UNPUBLISH = 'ProductAttachment.product_abstract_attachment.unpublish';
}
