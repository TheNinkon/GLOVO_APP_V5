// Configuración global de AJAX para enviar siempre el token CSRF
// Es importante que esto esté al principio del archivo.
$.ajaxSetup({
  headers: {
    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
  }
});

('use strict');

$(function () {
  const dt_basic_table = $('#riders-table');
  let dataTable; // Hacemos la variable accesible globalmente en este scope

  // Inicialización del DataTable
  if (dt_basic_table.length) {
    dataTable = dt_basic_table.DataTable({
      // Asignamos la instancia a nuestra variable
      processing: true,
      serverSide: true,
      ajax: '/admin/riders', // La URL que definimos en routes/web.php
      columns: [
        { data: 'id', name: 'id' },
        { data: 'full_name', name: 'full_name' },
        { data: 'dni', name: 'dni' },
        { data: 'city', name: 'city' },
        { data: 'email', name: 'email' },
        { data: 'status', name: 'status' },
        { data: 'action', name: 'action', orderable: false, searchable: false }
      ],
      // Opciones adicionales para que se vea bien en español
      language: {
        url: '//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Spanish.json'
      },
      // Para añadir botones (Exportar, etc.) en el futuro
      dom: '<"card-header"<"head-label text-center"><"dt-action-buttons text-end"B>><"d-flex justify-content-between align-items-center row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>t<"d-flex justify-content-between row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
      buttons: [
        // Aquí se pueden añadir botones de exportación si se necesita
      ]
    });
  }

  // Lógica para el borrado con SweetAlert2
  $(document).on('click', '.delete-rider-btn', function () {
    const deleteUrl = $(this).data('url');

    Swal.fire({
      title: '¿Estás seguro?',
      text: '¡No podrás revertir esta acción!',
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
          type: 'DELETE', // El método es DELETE
          success: function (response) {
            // Recargamos la tabla para que desaparezca el registro eliminado
            dataTable.ajax.reload();

            // Mostramos la alerta "toast" de éxito
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
            // En caso de error, mostramos una alerta de error
            Swal.fire({
              icon: 'error',
              title: 'Error',
              text: 'No se pudo eliminar el rider. Por favor, inténtalo de nuevo.',
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
