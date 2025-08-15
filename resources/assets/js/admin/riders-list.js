'use strict';

$(function () {
  const dt_basic_table = $('#riders-table');
  let dataTable; // Hacemos la variable accesible

  // Inicialización del DataTable
  if (dt_basic_table.length) {
    dataTable = dt_basic_table.DataTable({
      // Asignamos la instancia a nuestra variable
      processing: true,
      serverSide: true,
      ajax: '/admin/riders',
      columns: [
        { data: 'id', name: 'id' },
        { data: 'full_name', name: 'full_name' },
        { data: 'dni', name: 'dni' },
        { data: 'city', name: 'city' },
        { data: 'email', name: 'email' },
        { data: 'status', name: 'status' },
        { data: 'action', name: 'action', orderable: false, searchable: false }
      ],
      language: {
        url: '//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Spanish.json'
      }
    });
  }

  // ---- NUEVA LÓGICA PARA EL BORRADO ----
  $(document).on('click', '.delete-rider-btn', function () {
    const deleteUrl = $(this).data('url');

    Swal.fire({
      title: '¿Estás seguro?',
      text: '¡No podrás revertir esto!',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonText: 'Sí, ¡eliminar!',
      cancelButtonText: 'Cancelar',
      customClass: {
        confirmButton: 'btn btn-primary me-3',
        cancelButton: 'btn btn-label-secondary'
      },
      buttonsStyling: false
    }).then(function (result) {
      if (result.isConfirmed) {
        $.ajax({
          url: deleteUrl,
          type: 'DELETE',
          data: {
            _token: '{{ csrf_token() }}' // Laravel necesita el token CSRF
          },
          success: function (response) {
            // Recargamos la tabla para que desaparezca el registro
            dataTable.ajax.reload();

            // Mostramos la alerta bonita de éxito
            Swal.fire({
              icon: 'success',
              title: '¡Eliminado!',
              text: response.message,
              toast: true,
              position: 'top-end',
              showConfirmButton: false,
              timer: 3000,
              timerProgressBar: true
            });
          },
          error: function (xhr) {
            // Manejo de errores
            Swal.fire({
              icon: 'error',
              title: 'Error',
              text: 'No se pudo eliminar el rider.',
              customClass: {
                confirmButton: 'btn btn-primary'
              }
            });
          }
        });
      }
    });
  });
});
