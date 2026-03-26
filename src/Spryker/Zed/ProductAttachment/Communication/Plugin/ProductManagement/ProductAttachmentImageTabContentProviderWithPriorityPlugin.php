<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\ProductAttachment\Communication\Plugin\ProductManagement;

use Generated\Shared\Transfer\ProductAbstractTransfer;
use Spryker\Zed\Kernel\Communication\AbstractPlugin;
use Spryker\Zed\ProductManagementExtension\Dependency\Plugin\ProductAbstractFormTabContentProviderWithPriorityPluginInterface;

/**
 * @method \Spryker\Zed\ProductAttachment\Business\ProductAttachmentFacadeInterface getFacade()
 * @method \Spryker\Zed\ProductAttachment\ProductAttachmentConfig getConfig()
 * @method \Spryker\Zed\ProductAttachment\Communication\ProductAttachmentCommunicationFactory getFactory()
 */
class ProductAttachmentImageTabContentProviderWithPriorityPlugin extends AbstractPlugin implements ProductAbstractFormTabContentProviderWithPriorityPluginInterface
{
    protected const string TAB_NAME = 'image';

    protected const int PRIORITY = 10;

    protected const string TEMPLATE_PATH = '@ProductAttachment/Product/_partials/product-management-attachment-section.twig';

    /**
     * {@inheritDoc}
     * - Returns 'image' as the tab name.
     * - Image tab is defined in \Spryker\Zed\ProductManagement\Communication\Tabs\AbstractProductFormTabs::addImageTab.
     *
     * @api
     *
     * @return string
     */
    public function getTabName(): string
    {
        return static::TAB_NAME;
    }

    /**
     * {@inheritDoc}
     * - Returns the rendering priority within the image tab.
     *
     * @api
     *
     * @return int
     */
    public function getPriority(): int
    {
        return static::PRIORITY;
    }

    /**
     * {@inheritDoc}
     * - Provides attachment section template to be displayed in the image tab.
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\ProductAbstractTransfer|null $productAbstractTransfer
     *
     * @return array<string>
     */
    public function provideTabContent(?ProductAbstractTransfer $productAbstractTransfer = null): array
    {
        return [static::TEMPLATE_PATH];
    }
}
