let spinner = {
    show: function() {
        document.getElementById("spinner").classList.remove("d-none");
    },
    hide: function() {
        document.getElementById("spinner").classList.add("d-none");
    },
    toggle: function(show = true) {
        if (show) {
            this.show();
        } else {
            this.hide();
        }
    }
};

$(function () {
    let spinner = document.createElement('div');
    spinner.setAttribute('id', 'spinner');
    spinner.classList.add("d-none");
    spinner.innerHTML = '<div class="spinner-border text-info" role="status"><span class="visually-hidden">Ładowanie...</span></div>';
    document.getElementsByTagName('body')[0].appendChild(spinner);
});