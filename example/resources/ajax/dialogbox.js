let _bootstrapLayout = 'tailwind';

$.fn.dialogbox = function (event = 'create', config = {}) {
    if (typeof event === 'object') {
        config = event;
        event = 'create';
    }

    config.isDialogbox = $(this).hasClass('ui-dialog-content');
    config.id = config.isDialogbox
        ? $(this).attr('id')
        : (config.id ?? 'dialogbox_' + (Math.random() + 1).toString(36).substring(2));
    config.autoOpen = config.autoOpen ?? false;
    config.maxHeight = config.maxHeight ?? 700;
    config.title = config.title ?? 'Dialogox';
    config.load = config.load ?? $(this).attr('data-controller') ?? null;
    config.loadAsync = config.loadAsync ?? false;
    config.content = config.content ?? '';
    config.controller = config.controller ?? null;
    config.closeDestroy = config.closeDestroy ?? true;
    config.width = config.width ?? 300;

    if (config.load && config.load[0] === '/') {
        config.load = config.load + '&dialogbox=1';
    }

    switch (event) {
        case 'load':
            if (!config.isDialogbox) {
                return $(this);
            }

            jQuery.ajax({
                url: config.load,
                async: config.loadAsync,
                success: (result) => {
                    $(this).html(result);
                },
                error: () => {
                    $(this).html('Wystąpił błąd poddczas pobierania danych');
                }
            });

            $(this).attr('data-controller', config.load.replace('index.php?controller=', '').replaceAll('&dialogbox=1', ''));
            $(this).trigger('dialogLoad', this);
            break;
        case 'open':
            if (!config.isDialogbox) {
                return $(this);
            }

            $(this).dialog('open');
            $(this).trigger('dialogOpen', this);
            break;
        case 'close':
            if (!config.isDialogbox) {
                return $(this);
            }

            $(this).dialog('close');
            $(this).trigger('dialogClose', this);
            break;
        case 'destroy':
            if (!config.isDialogbox) {
                return $(this);
            }

            $(this).dialog('destroy').remove();
            $(this).trigger('dialogDestroy', this);
            break;
        case 'create':
            if (config.isDialogbox) {
                return $(this);
            }

            config.dialogClasses = {};

            switch (_bootstrapLayout) {
                case 'tailwind':
                    config.dialogClasses = {
                        "ui-dialog": "absolute bg-white rounded-lg shadow dark:bg-gray-700",
                        "ui-dialog-titlebar": "flex items-center justify-between p-4 md:p-5 border-b rounded-t dark:border-gray-600",
                        "ui-dialog-title": "text-xl font-semibold text-gray-900 dark:text-white",
                        "ui-dialog-titlebar-close": "text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white",
                        "ui-dialog-content": "p-4 md:p-5 space-y-4 dark:text-white",
                        "ui-dialog-buttonpane": "modal-footer"
                    };

                    break;
            }

            let $dialog = $('<div id="' + config.id + '">' + config.content + '</div>', 'body').dialog({
                title: config.title,
                width: config.width,
                classes: config.dialogClasses,
                position: { my: "center", at: "center", of: window },
                resize: function () {
                    switch (_bootstrapLayout) {
                        case 'bootstrap':
                            $(this).css('width', '100%');
                            break;
                    }

                    $(this).trigger('dialogResize', $("#" + config.id));
                },
                close: function () {
                    if (config.closeDestroy) {
                        $(this).dialog('destroy');
                    }
                }
            });

            if (config.autoOpen) {
                $dialog.dialog('open');
            }

            if (config.load) {
                $dialog.dialogbox('load', config);
            }

            if (config.controller) {
                $dialog.attr('data-controller', config.controller.replace('index.php?controller=', '').replaceAll('&dialogbox=1', ''));
            }

            $(this).trigger('dialogCreate', this);
            return $dialog;
        case 'reload':
            $(this).dialogbox('load', config);

            break;
        case 'setLayout':
            _bootstrapLayout = config;
            break;
    }

    return $(this);
};