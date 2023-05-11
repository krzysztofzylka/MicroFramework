let _bootstrapLayout = 'bootstrap';

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

                    $('[title]', $(this)).tooltip();
                },
                error: () => {
                    $(this).html('Failed load data');
                }
            });

            $(this).attr('data-controller', config.load.replace('index.php?url=', '').replace('&dialogbox=1', ''));
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
                case 'bootstrap':
                    config.dialogClasses = {
                        "ui-dialog": "modal-bootstrap5",
                        "ui-dialog-titlebar": "modal-header",
                        "ui-dialog-title": "modal-title",
                        "ui-dialog-titlebar-close": "close",
                        "ui-dialog-content": "modal-body",
                        "ui-dialog-buttonpane": "modal-footer"
                    };

                    break;
            }

            let $dialog = $('<div id="' + config.id + '">' + config.content + '</div>', 'body').dialog({
                title: config.title,
                width: config.width,
                classes: config.dialogClasses,
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
                $dialog.attr('data-controller', config.controller);
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

$.fn.ajaxlink = function (event, data = null) {
    switch (event) {
        case 'main':
            spinner.show();

            $.ajax({
                url: data.href ?? data,
                async: true,
                success: (result) => {
                    if (typeof result === 'object') {
                        $(this).ajaxlink('response', result);
                    } else {
                        let viewConfig = $(this).ajaxlink('_getConfig', result);

                        // if (typeof viewConfig !== 'object') {
                        //     VanillaToasts.create({
                        //         text: 'Wystąpił błąd podczas pobrania konfiguracji (objekt)',
                        //         type: 'error',
                        //         title: 'Błąd',
                        //         timeout: 3000
                        //     });
                        // } else if (!('layout' in viewConfig)) {
                        //     VanillaToasts.create({
                        //         text: 'Wystąpił błąd podczas pobrania konfiguracji (szablon)',
                        //         type: 'error',
                        //         title: 'Błąd',
                        //         timeout: 3000
                        //     });
                        // }

                        switch (viewConfig.layout) {
                            case 'ajax':
                                spinner.hide();

                                if (data.ajaxSelector !== 'undefined' && data.ajaxSelector !== null && data.ajaxSelector) {
                                    $(data.ajaxSelector).html(result);
                                }

                                return;
                            default:
                            case 'dialogbox':
                                $(document).dialogbox({
                                    content: result,
                                    autoOpen: true,
                                    title: viewConfig.title,
                                    controller: data,
                                    width: viewConfig.dialogboxWidth
                                });
                                break;
                        }
                    }

                    spinner.hide();
                },
                error: () => {
                    VanillaToasts.create({
                        text: 'Wystąpił błąd podczas pobierania danych',
                        type: 'error',
                        title: 'Błąd',
                        timeout: 3000
                    });
                    console.log('Wystąpił błąd podczas pobierania danych z ' + data);

                    spinner.hide();
                },
                done: () => {
                    spinner.hide();
                }
            });
            break;
        case 'form':
            spinner.show();

            if ($(this).is('form')) {
                let possible = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789',
                    id = $(this).attr('id');

                if (id === undefined) {
                    for (let i = 0; i < 20; i++) {
                        id += possible.charAt(Math.floor(Math.random() * possible.length));
                    }

                    $(this).attr('id', id);
                }

                let config = {
                    action: $(this).attr("action"),
                    data: new FormData(document.getElementById(id))
                }

                $.ajax({
                    url: config.action,
                    type: 'POST',
                    data: config.data,
                    processData: false,
                    contentType: false
                }).done((result) => {
                    if (typeof result === 'object') {
                        result.formElement = data;
                        $(this).find('.is-invalid').removeClass('is-invalid');
                        $(this).find('.invalid-feedback').remove();
                        $(this).find('.text-muted').removeClass('d-none');
                        $(this).ajaxlink('response', result);
                    } else {
                        VanillaToasts.create({
                            text: 'Wystąpił błąd poczas pobrania danych z formularza',
                            type: 'error',
                            title: 'Błąd',
                            timeout: 3000
                        });
                        console.log('Wystąpił błąd poczas pobrania danych z formularza', result);
                    }

                    spinner.hide();
                }).fail((e) => {
                    VanillaToasts.create({
                        text: 'Wystąpił błąd poczas pobrania danych z formularza',
                        type: 'error',
                        title: 'Błąd',
                        timeout: 3000
                    });
                    console.log('Wystąpił błąd poczas pobrania danych z formularza', e);

                    spinner.hide();
                });
            } else {
                VanillaToasts.create({
                    text: 'Wystąpił błąd poczas pobrania danych z formularza',
                    type: 'error',
                    title: 'Błąd',
                    timeout: 3000
                });
                console.log('Wystąpił błąd poczas pobrania danych z formularza', 'not form');

                spinner.hide();
            }
            break;
        case 'response':
            if (data.type === 'formValidatorErrorResponse') {
                $.each(data.list, function (name, value) {
                    let $element = $(data.formElement).find('[name="' + name + '"]');

                    $element.addClass('is-invalid');

                    if (typeof value === 'string') {
                        if ($element.parent().find('.invalid-feedback').length === 0) {
                            $element.parent().append('<div class="invalid-feedback">' + value + '</div>');
                        } else {
                            $element.parent().find('.invalid-feedback').html(value);
                        }

                        $element.parent().find('.text-muted').addClass('d-none');
                    }
                });

                return;
            }

            switch (data.ajaxLoaderConfig.layout) {
                case 'toast':
                    if (data.ajaxLoaderConfig.redirect !== false) {
                        $(this).ajaxlink('redirect', data.ajaxLoaderConfig.redirect);

                        return;
                    }

                    $(this).ajaxlink('toast', data);
                    break;
                default:
                    VanillaToasts.create({
                        text: 'Nie udało się wykonać akcji',
                        type: 'error',
                        title: 'Błąd',
                        timeout: 3000
                    });
                    break;
            }


            if (data.ajaxLoaderConfig.dialog.close) {
                $(this).closest('.ui-dialog-content').dialogbox('destroy');
            } else if (data.ajaxLoaderConfig.dialog.reload) {
                $(this).closest('.ui-dialog-content').dialogbox('reload');
            }

            if (data.ajaxLoaderConfig.pageReload) {
                let mainLoad = $('body:first').attr('data-load');

                $('.ajax-content').load(mainLoad + '&dialogbox=1');
            }
            break;
        case 'toast':
            if (data.message === '' && data.title === '') {
                return;
            }

            switch (data.type) {
                case 'WARNING':
                    VanillaToasts.create({
                        text: data.message,
                        type: 'warning',
                        title: data.title ?? null,
                        timeout: 3000
                    });
                    break;
                case 'OK':
                    VanillaToasts.create({
                        text: data.message,
                        type: 'success',
                        title: data.title ?? null,
                        timeout: 3000
                    });
                    break;
                case 'ERROR':
                case 'ERR':
                    VanillaToasts.create({
                        text: data.message,
                        type: 'error',
                        title: data.title ?? null,
                        timeout: 3000
                    });
                    break;
                default:
                    VanillaToasts.create({
                        text: data.message,
                        type: 'success',
                        title: data.title ?? null,
                        timeout: 3000
                    });
            }
            break;
        case 'redirect':
            document.location = data.replace('//', '/');
            break;
        case '_getConfig':
            let indexOf = data.indexOf("data-config='");

            if (indexOf === -1) {
                return {};
            }

            let firstLine = data.split('\n')[0];

            return JSON.parse(firstLine.substring(
                indexOf + "data-config='".length,
                firstLine.lastIndexOf("'")))
    }
};

$(document).ready(function () {
    $(document).on('click', '.ajaxlink', function (e) {
        e.preventDefault();

        let path = $(this).attr('href');

        if (typeof path === "undefined") {
            return false;
        }


        $(this).ajaxlink('main', {href: path.replace('//', '/'), ajaxSelector: $(this).attr('data-ajaxSelector')});
    });

    $(document).on('submit', 'form[action]:not(.disableAjax)', function (event) {
        event.preventDefault();
        event.stopImmediatePropagation();

        $(this).ajaxlink('form', $(this));
    });
});