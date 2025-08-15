'use strict';

$(function () {
  const dt_basic_table = $('#riders-table');

  if (dt_basic_table.length) {
    dt_basic_table.DataTable({
      processing: true,
      serverSide: true,
      ajax: '/admin/riders', // La URL de nuestro controlador
      columns: [
        { data: 'id', name: 'id' },
        { data: 'full_name', name: 'full_name' },
        { data: 'dni', name: 'dni' },
        { data: 'city', name: 'city' },
        { data: 'email', name: 'email' },
        { data: 'status', name: 'status' },
        { data: 'action', name: 'action', orderable: false, searchable: false }
      ],
      // Opciones adicionales (idioma, etc.)
      language: {
        url: '//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Spanish.json'
      }
    });
  }
});
