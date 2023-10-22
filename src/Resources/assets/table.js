$.fn.table = function () {
    function reloadTable($table, $form = null, data = null) {
        spinner.show();

        console.log($form);

        const uri = $table.attr('controller'),
            formData = $form.serialize();

        $.ajax({
            type: "POST",
            url: uri,
            data: data ?? formData,
            success: (response) => {
                $table.html(response);

                spinner.hide();
            }
        });
    }

    $(this).on('click', 'input[name="page"]', (e) => {
        const $form = $(e.currentTarget).closest('form');

        reloadTable($(this), $form, $form.serialize() + '&page=' + $(e.currentTarget).attr('value'));

        e.preventDefault();
        return false;
    });

    $(this).on('submit', 'form', (e) => {
        const $form = $(e.currentTarget);

        reloadTable($(this), $form);

        e.preventDefault();
        return false;
    });

    $(this).on('change', 'select', (e) => {
        const $form = $(e.currentTarget).closest('form');

        reloadTable($(this), $form);

        e.preventDefault();
        return false;
    });

    $(this).find('.user-actions').on('click', 'a', (e) => {
        let $button = $(e.currentTarget),
            data = '',
            href = $button.attr('href');

        if ($button.attr('data-href') !== undefined) {
            href = $button.attr('data-href');
        } else {
            $button.attr('data-href', href);
        }

        $('._checkbox_:checked').each(function(id,element) {
            data += (data !== '' ? ',' : '') + $(element).attr('data-id')
        });

        if (data !== '') {
            href += '/' + data;
        }

        $button.attr('href', href);
    });
};