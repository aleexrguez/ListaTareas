$('.finalizar').on('click', function (e) {
    e.preventDefault();
    var $this = $(this),
        url = $this.data('url'),
        $titulo = $this.closest('tr').find('.titulo')
    textoContrario = $this.data('textoContrario');
    textoActual = $this.text();

    $this.addClass('disabled');

    $.post(url, {})
        .done(function (respuesta) {
            if (respuesta.finalizada) {
                $titulo.html(respuesta.finalizada ? '<s>' + $titulo.text() + '</s>' : $titulo.text());
            } else {
                $titulo.html($titulo.text());
            }
            $this.text(textoContrario);
            $this.data('textoContrario', textoActual);
            $this.removeClass('disabled');
        })
        .fail(function () {
            $this.removeClass('disabled');
        });
});