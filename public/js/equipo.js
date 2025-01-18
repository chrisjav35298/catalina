// Obtén los campos select
const staffSelect = document.getElementById('staff-select');
const puestoSelect = document.getElementById('equipo_puesto');

// Define las opciones disponibles para cada categoría
const opciones = {
    comisionDirectiva: [
        { value: 'presidente', text: 'Presidente' },
        { value: 'secretario/a', text: 'Secretario/a' },
        { value: 'tesorero', text: 'Tesorero' }
    ],
    equipoLegal: [
        { value: 'areaCivil', text: 'Área civil' },
        { value: 'areaPenal', text: 'Área penal' }
    ],
    colaboradores: [
        { value: 'colaborador/a', text: 'Colaborador' }
    ]
};

// Función para actualizar las opciones de 'puesto'
const actualizarPuesto = (categoria) => {
    // Limpia todas las opciones existentes
    puestoSelect.innerHTML = '';

    // Agrega las nuevas opciones según la categoría seleccionada
    if (opciones[categoria]) {
        opciones[categoria].forEach(opcion => {
            const opt = document.createElement('option');
            opt.value = opcion.value;
            opt.textContent = opcion.text;
            puestoSelect.appendChild(opt);
        });
    }
};

// Escucha cambios en el campo 'staff'
equipo_staff.addEventListener('change', (event) => {
    const categoria = event.target.value;
    actualizarPuesto(categoria);
});

// Inicializa con las opciones de la selección actual
actualizarPuesto(equipo_staff.value);
