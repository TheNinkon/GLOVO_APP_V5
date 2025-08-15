'use strict';

document.addEventListener('DOMContentLoaded', function () {
  const summaryPanel = document.getElementById('schedule-summary');
  // Si el panel de resumen no existe en la página, no hacemos nada.
  if (!summaryPanel) return;

  const contractHours = parseFloat(summaryPanel.dataset.contractHours);
  const counterElement = document.getElementById('selected-hours-counter');
  const checkboxes = document.querySelectorAll('.slot-checkbox');
  const form = document.querySelector('form'); // El formulario que contiene los checkboxes

  /**
   * Actualiza el contador de horas y el estado de los checkboxes.
   */
  function updateCounterAndAvailability() {
    const checkedSlots = document.querySelectorAll('.slot-checkbox:checked');
    const selectedHours = checkedSlots.length * 0.5;

    // 1. Actualizar el texto del contador
    if (counterElement) {
      counterElement.textContent = selectedHours.toFixed(1);
    }

    // 2. Lógica para deshabilitar checkboxes si se alcanza el límite
    if (contractHours > 0) {
      if (selectedHours >= contractHours) {
        if (counterElement) {
          counterElement.classList.add('over-limit');
        }
        // Deshabilitar todos los checkboxes que NO estén seleccionados
        checkboxes.forEach(box => {
          if (!box.checked) {
            box.disabled = true;
            // Añadimos una clase visual para que sea más obvio
            if (box.closest('.slot')) {
              box.closest('.slot').classList.add('slot-disabled-by-limit');
            }
          }
        });
      } else {
        if (counterElement) {
          counterElement.classList.remove('over-limit');
        }
        // Habilitar todos los checkboxes disponibles que no estén llenos por demanda
        checkboxes.forEach(box => {
          const parentSlot = box.closest('.slot');
          if (parentSlot && !parentSlot.classList.contains('slot-full')) {
            box.disabled = false;
            parentSlot.classList.remove('slot-disabled-by-limit');
          }
        });
      }
    }
  }

  // Añadir un event listener a cada celda de la tabla para hacerla clickeable
  document.querySelectorAll('.slot-available, .slot-mine').forEach(cell => {
    cell.addEventListener('click', function (e) {
      const checkbox = this.querySelector('.slot-checkbox');
      // Solo actuar si el checkbox existe y no está deshabilitado
      if (checkbox && !checkbox.disabled) {
        // Invertimos el estado del checkbox
        checkbox.checked = !checkbox.checked;

        // Disparamos un evento 'change' manualmente para que otros listeners reaccionen
        checkbox.dispatchEvent(new Event('change'));
      }
    });
  });

  // Reaccionar al cambio de cualquier checkbox para actualizar todo
  form.addEventListener('change', function (e) {
    if (e.target.classList.contains('slot-checkbox')) {
      const parentCell = e.target.closest('.slot');
      // Actualizamos visualmente la celda
      if (e.target.checked) {
        parentCell.classList.add('slot-mine');
      } else {
        parentCell.classList.remove('slot-mine');
      }
      // Actualizamos el contador y la disponibilidad del resto de celdas
      updateCounterAndAvailability();
    }
  });

  // Llamada inicial para establecer el estado correcto al cargar la página
  updateCounterAndAvailability();
});
