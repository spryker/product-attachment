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

    public function provideBusinessLayerDependencies(Container $container): Container
    {
        $container = parent::provideBusinessLayerDependencies($container);
        $container = $this->addProductAbstractQuery($container);
        $container = $this->addLocaleQuery($container);

        return $container;
    }

    public function provideCommunicationLayerDependencies(Container $container): Container
    {
        $container = parent::provideCommunicationLayerDependencies($container);
        $container = $this->addLocaleFacade($container);

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
}
