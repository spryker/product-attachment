/**
 * Copyright (c) 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

'use strict';

var initFormattedNumber = require('ZedGuiModules/libs/formatted-number-input');

document.addEventListener('DOMContentLoaded', function () {
    function sanitizeHTML(html) {
        const template = document.createElement('template');
        template.innerHTML = html;

        template.content.querySelectorAll('script').forEach((script) => script.remove());

        return template;
    }

    /**
     * @param event
     */
    function addAnotherAttachment(event) {
        event.preventDefault();

        const button = event.target.closest('.add-another-attachment');
        const container = button.closest('.attachment-container');

        const attachmentIndex = container.querySelectorAll('div.attachment-item').length;

        const newAttachmentHTML = container.dataset.attachmentPrototype
            .replace(/__attachment_name__/g, attachmentIndex)
            .trim();

        const template = sanitizeHTML(newAttachmentHTML);

        const newElements = Array.from(template.content.childNodes);

        button.before(template.content);

        newElements.forEach((element) => initFormattedNumber(element));
    }

    /**
     * @param event
     */
    function deleteAttachment(event) {
        event.preventDefault();

        event.target.closest('.attachment-item').remove();
    }

    /**
     * Register event listeners
     */
    document.body.addEventListener('click', function (event) {
        if (event.target.closest('.add-another-attachment')) {
            addAnotherAttachment(event);
        }

        if (event.target.closest('.remove-attachment')) {
            deleteAttachment(event);
        }
    });
});
