document.addEventListener("DOMContentLoaded", function () {
    $('#datatable-listas').DataTable({
        "language": {
            "lengthMenu": "Mostrar _MENU_ registros por página",
            "zeroRecords": "No se encontraron resultados",
            "info": "Mostrando página _PAGE_ de _PAGES_",
            "infoEmpty": "No hay registros disponibles",
            "infoFiltered": "(filtrado de _MAX_ registros totales)",
            "search": "Buscar:",
            "paginate": {
                "first": "Primero",
                "last": "Último",
                "next": "Siguiente",
                "previous": "Anterior"
            }
        }
    });

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
});