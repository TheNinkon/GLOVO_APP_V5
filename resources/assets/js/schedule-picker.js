'use strict';

document.addEventListener('DOMContentLoaded', function () {
  const summaryPanel = document.getElementById('schedule-summary');
  if (!summaryPanel) return;

  const contractHours = parseFloat(summaryPanel.dataset.contractHours) || 0;
  const counterElement = document.getElementById('selected-hours-counter');
  const allCheckboxes = document.querySelectorAll('.slot-checkbox');

  function updateCounterAndAvailability() {
    const checkedCheckboxes = document.querySelectorAll('.slot-checkbox:checked');
    const selectedHours = checkedCheckboxes.length * 0.5;

    if (counterElement) {
      counterElement.textContent = selectedHours.toFixed(1);
    }

    const isOverLimit = contractHours > 0 && selectedHours >= contractHours;

    if (counterElement) {
      counterElement.classList.toggle('over-limit', isOverLimit);
    }

    // Recorremos todos los checkboxes para actualizar su estado
    allCheckboxes.forEach(box => {
      const parentSlot = box.closest('.slot-item');
      // Un turno se deshabilita si NO está seleccionado Y (está lleno por demanda O se superó el límite)
      if (!box.checked) {
        const isDisabledByDemand = parentSlot.classList.contains('unavailable');
        box.disabled = isDisabledByDemand || isOverLimit;
      }
    });
  }

  // Añadimos el listener de 'change' a cada checkbox
  allCheckboxes.forEach(checkbox => {
    checkbox.addEventListener('change', function () {
      const parentLabel = this.closest('.slot-item');
      if (parentLabel) {
        parentLabel.classList.toggle('selected', this.checked);
      }
      updateCounterAndAvailability();
    });
  });

  // Llamada inicial para establecer el estado correcto al cargar la página
  updateCounterAndAvailability();
});
