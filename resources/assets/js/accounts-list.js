// Configuración global de AJAX para enviar siempre el token CSRF
$.ajaxSetup({
  headers: {
    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
  }
});

('use strict');

$(function () {
  const dt_table = $('#accounts-table');
  let dataTable;

  // --- Alerta de éxito si viene desde la sesión (inyectada en la vista) ---
  const successMessage = $('.card').data('success-message');
  if (successMessage) {
    Swal.fire({
      icon: 'success',
      title: '¡Hecho!',
      text: successMessage,
      toast: true,
      position: 'top-end',
      showConfirmButton: false,
      timer: 3000,
      timerProgressBar: true
    });
  }

  // Inicialización DataTables
  if (dt_table.length) {
    dataTable = dt_table.DataTable({
      processing: true,
      serverSide: true,
      ajax: '/admin/accounts',
      columns: [
        { data: 'id', name: 'id' },
        { data: 'courier_id', name: 'courier_id' },
        { data: 'email', name: 'email' },
        { data: 'city', name: 'city' },
        { data: 'status', name: 'status' },
        { data: 'assigned_to', name: 'assigned_to', orderable: false, searchable: false }, // <-- NUEVA COLUMNA
        { data: 'action', name: 'action', orderable: false, searchable: false }
      ],
      order: [[0, 'desc']]
    });
  }

  // Borrado con confirmación
  $(document).on('click', '.delete-account-btn', function () {
    const deleteUrl = $(this).data('url');

    Swal.fire({
      title: '¿Estás seguro?',
      text: '¡No podrás revertir esta acción!',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonText: 'Sí, ¡eliminar!',
      cancelButtonText: 'Cancelar',
      customClass: { confirmButton: 'btn btn-primary me-3', cancelButton: 'btn btn-label-secondary' },
      buttonsStyling: false
    }).then(function (result) {
      if (result.isConfirmed) {
        $.ajax({
          url: deleteUrl,
          type: 'DELETE',
          success: function (response) {
            if (dataTable) dataTable.ajax.reload(null, false);
            Swal.fire({
              icon: 'success',
              title: '¡Eliminado!',
              text: response?.message || 'Cuenta eliminada con éxito.',
              toast: true,
              position: 'top-end',
              showConfirmButton: false,
              timer: 3000,
              timerProgressBar: true
            });
          },
          error: function () {
            Swal.fire({
              icon: 'error',
              title: 'Error',
              text: 'No se pudo eliminar la cuenta.'
            });
          }
        });
      }
    });
  });
});
