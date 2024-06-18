let isAuthenticated = false;
let loggedUserId = null;
// Función para manejar el inicio de sesión
function login(username, password) {
  // Aquí deberías implementar la lógica para verificar las credenciales
  // contra una base de datos o un servicio de autenticación
  // Por simplicidad, supongamos que cualquier usuario y contraseña son válidos
  isAuthenticated = true;
  loggedUserId = username; // Establecer el userId como el nombre de usuario 
}

// Agregar evento de escucha al formulario de inicio de sesión
const loginForm = document.querySelector('#login-form form');
loginForm.addEventListener('submit', e => {
  e.preventDefault();
  const username = document.getElementById('username').value;
  const password = document.getElementById('password').value;

  login(username, password);

  // Limpiar el formulario de inicio de sesión
  loginForm.reset();
});

// Función para cargar los tickets desde el localStorage
function loadTickets() {
    const ticketsData = localStorage.getItem(`tickets_${loggedUserId}`);
    return ticketsData ? JSON.parse(ticketsData) : [];
  }
  
  // Datos de tickets
  let tickets = loadTickets();
  
  // Función para renderizar los tickets
  function renderTickets() {
    const ticketList = document.getElementById('tickets');
    ticketList.innerHTML = '';
  
    tickets.forEach(ticket => {
      if (ticket.userId === loggedUserId) {
        const li = document.createElement('li');
        li.textContent = `${ticket.id}. ${ticket.subject} (${ticket.status})`;
        li.addEventListener('click', () => showTicketDetails(ticket));
        ticketList.appendChild(li);
      }
    });
  }
  
  // Función para mostrar los detalles de un ticket
  function showTicketDetails(ticket) {
    const ticketDetails = document.getElementById('ticket-details');
    ticketDetails.innerHTML = `
      <h3>${ticket.subject}</h3>
      <p>${ticket.description}</p>
      <p>Estado: ${ticket.status}</p>
    `;
  }
  
  // Renderizar los tickets al cargar la página
  renderTickets();
  
  // Manejar el envío del formulario de nuevo ticket
  const form = document.querySelector('#ticket-form form');
  form.addEventListener('submit', e => {
  e.preventDefault();

  if (!isAuthenticated) {
    alert('Debes iniciar sesión para poder ingresar un ticket');
    return;
  }

  const subject = document.getElementById('subject').value;
  const description = document.getElementById('description').value;
  
    // Agregar el nuevo ticket a la lista
    const newTicket = {
      id: tickets.length + 1,
      subject,
      description,
      status: 'abierto',
      userId: loggedUserId
    };
    tickets.push(newTicket);
  
    // Guardar los tickets en el localStorage
    localStorage.setItem(`tickets_${loggedUserId}`, JSON.stringify(tickets));
  
    // Limpiar el formulario
    form.reset();
  
    // Renderizar los tickets actualizados
    renderTickets();
  });
  