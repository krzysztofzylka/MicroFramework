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
};