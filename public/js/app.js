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
                let nuevoIcono = respuesta.finalizada
                    ? '<i class="fas fa-undo"></i>' 
                    : '<i class="fas fa-check"></i>'; 

                $this.html(nuevoIcono);
                $this.attr('title', textoContrario);
                $this.data('textoContrario', textoActual);
                $this.removeClass('disabled');
            })
            .fail(function () {
                $this.removeClass('disabled');
            });
    });

    let tipoAEliminar = null;
    let idAEliminar = null;

    $(document).on('click', '.eliminar-tarea', function () {
        idAEliminar = $(this).data('id');
        tipoAEliminar = 'tarea';
        const nombre = $(this).data('nombre');

        $('#nombre-objeto').text(nombre);
        $('#tipo-objeto').text('la tarea');

        const modal = new bootstrap.Modal(document.getElementById('modalEliminar'));
        modal.show();
    });

    $('#btn-confirmar-eliminar').on('click', function () {
        if (!idAEliminar || !tipoAEliminar) return;

        let url = '';

        if (tipoAEliminar === 'tarea') {
            url = `/tarea/${idAEliminar}/eliminar`;
        } else if (tipoAEliminar === 'lista') {
            url = `/lista/${idAEliminar}/eliminar`;
        }

        fetch(url, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                const table = $('#datatable-listas').DataTable();
                const fila = $(`button[data-id="${idAEliminar}"]`).closest('tr');
                table.row(fila).remove().draw();

                const modal = bootstrap.Modal.getInstance(document.getElementById('modalEliminar'));
                modal.hide();
            } else {
                alert('Error al eliminar.');
            }
        })
        .catch(err => {
            alert('Error en la solicitud.');
            console.error(err);
        });
    });
});
