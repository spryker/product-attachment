<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\ProductAttachment;

use Orm\Zed\Locale\Persistence\SpyLocaleQuery;
use Orm\Zed\Product\Persistence\SpyProductAbstractQuery;
use Spryker\Zed\Kernel\AbstractBundleDependencyProvider;
use Spryker\Zed\Kernel\Container;

class ProductAttachmentDependencyProvider extends AbstractBundleDependencyProvider
{
    public const string PROPEL_QUERY_PRODUCT_ABSTRACT = 'PROPEL_QUERY_PRODUCT_ABSTRACT';

    public const string PROPEL_QUERY_LOCALE = 'PROPEL_QUERY_LOCALE';

    public const string FACADE_LOCALE = 'FACADE_LOCALE';

    public const string PLUGINS_PRODUCT_ATTACHMENT_COLLECTION_EXPANDER = 'PLUGINS_PRODUCT_ATTACHMENT_COLLECTION_EXPANDER';

    public const string PLUGINS_PRODUCT_ATTACHMENT_COLLECTION_POST_SAVE = 'PLUGINS_PRODUCT_ATTACHMENT_COLLECTION_POST_SAVE';

    public const string PLUGINS_PRODUCT_ATTACHMENT_FORM_EXPANDER = 'PLUGINS_PRODUCT_ATTACHMENT_FORM_EXPANDER';

    public function provideBusinessLayerDependencies(Container $container): Container
    {
        $container = parent::provideBusinessLayerDependencies($container);
        $container = $this->addProductAbstractQuery($container);
        $container = $this->addLocaleQuery($container);
        $container = $this->addProductAttachmentCollectionExpanderPlugins($container);
        $container = $this->addProductAttachmentCollectionPostSavePlugins($container);

        return $container;
    }

    public function provideCommunicationLayerDependencies(Container $container): Container
    {
        $container = parent::provideCommunicationLayerDependencies($container);
        $container = $this->addLocaleFacade($container);
        $container = $this->addProductAttachmentFormExpanderPlugins($container);

        return $container;
    }

    protected function addProductAbstractQuery(Container $container): Container
    {
        $container->set(static::PROPEL_QUERY_PRODUCT_ABSTRACT, $container->factory(function (): SpyProductAbstractQuery {
            return SpyProductAbstractQuery::create();
        }));

        return $container;
    }

    protected function addLocaleQuery(Container $container): Container
    {
        $container->set(static::PROPEL_QUERY_LOCALE, $container->factory(function (): SpyLocaleQuery {
            return SpyLocaleQuery::create();
        }));

        return $container;
    }

    protected function addLocaleFacade(Container $container): Container
    {
        $container->set(static::FACADE_LOCALE, function (Container $container) {
            return $container->getLocator()->locale()->facade();
        });

        return $container;
    }

    protected function addProductAttachmentCollectionExpanderPlugins(Container $container): Container
    {
        $container->set(static::PLUGINS_PRODUCT_ATTACHMENT_COLLECTION_EXPANDER, function (): array {
            return $this->getProductAttachmentCollectionExpanderPlugins();
        });

        return $container;
    }

    protected function addProductAttachmentCollectionPostSavePlugins(Container $container): Container
    {
        $container->set(static::PLUGINS_PRODUCT_ATTACHMENT_COLLECTION_POST_SAVE, function (): array {
            return $this->getProductAttachmentCollectionPostSavePlugins();
        });

        return $container;
    }

    protected function addProductAttachmentFormExpanderPlugins(Container $container): Container
    {
        $container->set(static::PLUGINS_PRODUCT_ATTACHMENT_FORM_EXPANDER, function (): array {
            return $this->getProductAttachmentFormExpanderPlugins();
        });

        return $container;
    }

    /**
     * @return list<\Spryker\Zed\ProductAttachmentExtension\Dependency\Plugin\ProductAttachmentCollectionExpanderPluginInterface>
     */
    protected function getProductAttachmentCollectionExpanderPlugins(): array
    {
        return [];
    }

    /**
     * @return list<\Spryker\Zed\ProductAttachmentExtension\Dependency\Plugin\ProductAttachmentCollectionPostSavePluginInterface>
     */
    protected function getProductAttachmentCollectionPostSavePlugins(): array
    {
        return [];
    }

    /**
     * @return list<\Spryker\Zed\ProductAttachmentExtension\Dependency\Plugin\ProductAttachmentFormExpanderPluginInterface>
     */
    protected function getProductAttachmentFormExpanderPlugins(): array
    {
        return [];
    }
}
