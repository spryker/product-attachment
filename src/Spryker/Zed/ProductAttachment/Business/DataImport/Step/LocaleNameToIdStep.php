<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\ProductAttachment\Business\DataImport\Step;

use Orm\Zed\Locale\Persistence\SpyLocaleQuery;
use Spryker\Zed\DataImport\Business\Exception\EntityNotFoundException;
use Spryker\Zed\DataImport\Business\Model\DataImportStep\DataImportStepInterface;
use Spryker\Zed\DataImport\Business\Model\DataSet\DataSetInterface;
use Spryker\Zed\ProductAttachment\Business\DataImport\DataSet\ProductAttachmentDataSetInterface;

class LocaleNameToIdStep implements DataImportStepInterface
{
    /**
     * @var array<string, int|null>
     */
    protected array $idLocaleCache = [];

    public function __construct(protected SpyLocaleQuery $localeQuery)
    {
    }

    public function execute(DataSetInterface $dataSet): void
    {
        $locale = $dataSet[ProductAttachmentDataSetInterface::COLUMN_LOCALE];

        if ($locale === '' || $locale === null || $locale === 'default') {
            $dataSet[ProductAttachmentDataSetInterface::KEY_ID_LOCALE] = null;

            return;
        }

        if (isset($this->idLocaleCache[$locale])) {
            $dataSet[ProductAttachmentDataSetInterface::KEY_ID_LOCALE] = $this->idLocaleCache[$locale];

            return;
        }

        $localeEntity = $this->localeQuery
            ->clear()
            ->filterByLocaleName($locale)
            ->findOne();

        if ($localeEntity === null) {
            throw new EntityNotFoundException(sprintf(
                'Locale "%s" not found. Only valid locale names or "default" are allowed.',
                $locale,
            ));
        }

        $idLocale = $localeEntity->getIdLocale();
        $this->idLocaleCache[$locale] = $idLocale;
        $dataSet[ProductAttachmentDataSetInterface::KEY_ID_LOCALE] = $idLocale;
    }
}
