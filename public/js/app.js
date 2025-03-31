document.addEventListener("DOMContentLoaded", function () {
    if ($('#datatable-listas tbody tr').length > 0) {
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
            },
            "bAutoWidth": false
        });
    }

    const flashContainer = document.getElementById('flash-messages');
    if (flashContainer) {
        const mostrarMensajes = (tipo) => {
            const mensajes = flashContainer.dataset[tipo];
            if (mensajes) {
                mensajes.split('|').forEach(m => {
                    if (m.trim() !== '') mostrarToast(m, tipo);
                });
            }
        };

        mostrarMensajes('success');
        mostrarMensajes('warning');
        mostrarMensajes('error');
    }

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

    $(document).on('click', '.eliminar-lista', function () {
        idAEliminar = $(this).data('id');
        tipoAEliminar = 'lista';
        const nombre = $(this).data('nombre');
    
        $('#nombre-objeto').text(nombre);
        $('#tipo-objeto').text('la lista');
    
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
        
                mostrarToast("Elemento eliminado correctamente", "success");
            } else {
                mostrarToast("Error al eliminar", "error");
            }
        })
        .catch(err => {
            mostrarToast("Error en la solicitud", "error");
            console.error(err);
        });        
    });
});

function mostrarToast(mensaje, tipo = "success") {
    const colores = {
        success: 'bg-success text-white',
        error: 'bg-danger text-white',
        warning: 'bg-warning text-dark',
        info: 'bg-info text-white'
    };

    const toast = $(`
        <div class="toast align-items-center ${colores[tipo]} border-0" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body">
                    ${mensaje}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Cerrar"></button>
            </div>
        </div>
    `);

    $('#toast-container').append(toast);
    const bsToast = new bootstrap.Toast(toast[0]);
    bsToast.show();

    toast.on('hidden.bs.toast', function () {
        toast.remove();
    });
}

