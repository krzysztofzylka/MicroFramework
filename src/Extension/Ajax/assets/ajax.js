$.fn.ajaxlink = function (event, data = null) {
    switch (event) {
        case 'main':
            spinner.show();

            $.ajax({
                url: data + '?dialogbox=true',
                async: true,
                success: (result) => {
                    if (typeof result === 'object') {
                        $(this).ajaxlink('response', result);
                    } else {
                        let lines = result.split('\n'),
                            scriptContent = lines[0],
                            jsonString = scriptContent.replace("<script>var config =", "").replace(";</script>", "").trim().slice(1, -1),
                            config = JSON.parse(jsonString);

                        $(document).dialogbox({
                            content: result,
                            autoOpen: true,
                            title: config.title,
                            controller: data,
                            width: config.width
                        });
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
        // case 'form':
        //     // spinner.show();
        //
        //     if ($(this).is('form')) {
        //         let config = {
        //             action: $(this).attr("action"),
        //             data: $(this).serialize()
        //         }
        //
        //         $.ajax({
        //             url: config.action,
        //             type: 'POST',
        //             data: config.data,
        //         }).done((result) => {
        //             if (typeof result === 'object') {
        //                 result.formElement = data;
        //                 $(this).find('.is-invalid').removeClass('is-invalid');
        //                 $(this).find('.invalid-feedback').remove();
        //                 $(this).find('.text-muted').removeClass('d-none');
        //                 $(this).ajaxlink('response', result);
        //             } else {
        //                 VanillaToasts.create({
        //                     text: 'Wystąpił błąd poczas pobrania danych z formularza',
        //                     type: 'error',
        //                     title: 'Błąd',
        //                     timeout: 3000
        //                 });
        //                 console.log('Wystąpił błąd poczas pobrania danych z formularza', result);
        //             }
        //
        //             // spinner.hide();
        //         }).fail((e) => {
        //             VanillaToasts.create({
        //                 text: 'Wystąpił błąd poczas pobrania danych z formularza',
        //                 type: 'error',
        //                 title: 'Błąd',
        //                 timeout: 3000
        //             });
        //             console.log('Wystąpił błąd poczas pobrania danych z formularza', e);
        //
        //             // spinner.hide();
        //         });
        //     } else {
        //         VanillaToasts.create({
        //             text: 'Wystąpił błąd poczas pobrania danych z formularza',
        //             type: 'error',
        //             title: 'Błąd',
        //             timeout: 3000
        //         });
        //         console.log('Wystąpił błąd poczas pobrania danych z formularza', 'not form');
        //
        //         // spinner.hide();
        //     }
        //     break;
        case 'response':
            // if (data.type === 'formValidatorErrorResponse') {
            //     $.each(data.list, function (name, value) {
            //         console.log(name, value);
            //         let $element = $(data.formElement).find('[name="' + name + '"]');
            //
            //         if (!value[0]) {
            //             $element.removeClass('is-invalid');
            //             $element.parent().find('.invalid-feedback').remove();
            //             $element.parent().find('.text-muted').removeClass('d-none');
            //         } else {
            //             $element.addClass('is-invalid');
            //
            //             if (typeof value[1] === 'string') {
            //                 if ($element.parent().find('.invalid-feedback').length === 0) {
            //                     $element.parent().append('<div class="invalid-feedback">' + value[1] + '</div>');
            //                 } else {
            //                     $element.parent().find('.invalid-feedback').html(value[1]);
            //                 }
            //
            //                 $element.parent().find('.text-muted').addClass('d-none');
            //             }
            //         }
            //     });
            //
            //     return;
            // }

            switch (data.layout) {
                case 'redirect':
                    console.log(data);
                    $(this).ajaxlink('redirect', data.url);

                    break;
                case 'toast':
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


            try {
                if (data.dialog.close) {
                    console.log('close', $(this));
                    $(this).closest('.ui-dialog-content').dialogbox('destroy');
                } else if (data.dialog.reload) {
                    $(this).closest('.ui-dialog-content').dialogbox('reload');
                }
            } catch (e) {
            }

            try {
                if (data.pageReload) {
                    let mainLoad = $('body:first').attr('data-load');

                    $('main:first').load(mainLoad + '&dialogbox=1');
                }
            } catch (e) {
            }
            break;
        case 'toast':
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
            document.location = data;
            break;
        // case '_getConfig':
        //     let indexOf = data.indexOf("data-config='");
        //
        //     if (indexOf === -1) {
        //         return {};
        //     }
        //
        //     let firstLine = data.split('\n')[0];
        //
        //     return JSON.parse(firstLine.substring(
        //         indexOf + "data-config='".length,
        //         firstLine.lastIndexOf("'")))
    }
}

$(function () {
    $(document).on('click', '.ajaxlink', function (e) {
        e.preventDefault();

        $(this).ajaxlink('main', $(this).attr('href'));
    });

    $(document).on('submit', 'form[action]:not(.disableAjax)', function (event) {
        event.preventDefault();
        event.stopImmediatePropagation();

        $(this).ajaxlink('form', $(this));
    });
});