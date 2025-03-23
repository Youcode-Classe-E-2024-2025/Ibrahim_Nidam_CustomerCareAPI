<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ticket Management System</title>
    <!-- Alpine.js CDN -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="bg-gray-100 min-h-screen">
    <div x-data="app()" x-cloak>
        <!-- Navigation Bar -->
        <nav class="bg-gray-800 text-white p-4">
            <div class="container mx-auto flex justify-between items-center">
                <h1 class="text-xl font-bold">Ticket Management System</h1>
                <div x-show="isAuthenticated">
                    <span x-text="userData.name" class="mr-4"></span>
                    <button @click="logout" class="bg-red-600 hover:bg-red-700 px-3 py-1 rounded">Logout</button>
                </div>
            </div>
        </nav>

        <!-- Main Content Area -->
        <div class="container mx-auto p-4">
            <!-- Authentication Forms -->
            <div x-show="!isAuthenticated" class="grid md:grid-cols-2 gap-8">
                <!-- Login Form -->
                <div class="bg-white p-6 rounded shadow">
                    <h2 class="text-xl font-semibold mb-4">Login</h2>
                    <form @submit.prevent="login">
                        <div class="mb-4">
                            <label class="block mb-1">Email:</label>
                            <input type="email" x-model="loginForm.email" required class="w-full p-2 border rounded">
                        </div>
                        <div class="mb-4">
                            <label class="block mb-1">Password:</label>
                            <input type="password" x-model="loginForm.password" required class="w-full p-2 border rounded">
                        </div>
                        <div x-show="loginError" class="text-red-600 mb-4" x-text="loginError"></div>
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">Login</button>
                    </form>
                </div>

                <!-- Registration Form -->
                <div class="bg-white p-6 rounded shadow">
                    <h2 class="text-xl font-semibold mb-4">Register</h2>
                    <form @submit.prevent="register">
                        <div class="mb-4">
                            <label class="block mb-1">Name:</label>
                            <input type="text" x-model="registerForm.name" required class="w-full p-2 border rounded">
                        </div>
                        <div class="mb-4">
                            <label class="block mb-1">Email:</label>
                            <input type="email" x-model="registerForm.email" required class="w-full p-2 border rounded">
                        </div>
                        <div class="mb-4">
                            <label class="block mb-1">Password:</label>
                            <input type="password" x-model="registerForm.password" required class="w-full p-2 border rounded">
                        </div>
                        <div class="mb-4">
                            <label class="block mb-1">Confirm Password:</label>
                            <input type="password" x-model="registerForm.password_confirmation" required class="w-full p-2 border rounded">
                        </div>
                        <div class="mb-4">
                            <label class="block mb-1">Agent Code (optional):</label>
                            <input type="text" x-model="registerForm.agent_code" class="w-full p-2 border rounded">
                            <p class="text-xs text-gray-500 mt-1">Enter agent code to register as an agent</p>
                        </div>
                        <div x-show="registerError" class="text-red-600 mb-4" x-text="registerError"></div>
                        <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded">Register</button>
                    </form>
                </div>
            </div>

            <!-- Ticket Management (shown only when authenticated) -->
            <div x-show="isAuthenticated" class="mt-8">
                <!-- Tabs for different sections -->
                <div class="mb-6 border-b border-gray-200">
                    <ul class="flex flex-wrap -mb-px text-sm font-medium text-center">
                        <li class="mr-2">
                            <a href="#" @click.prevent="activeTab = 'myTickets'" 
                               :class="{'border-b-2 border-blue-600 text-blue-600': activeTab === 'myTickets', 'hover:text-gray-600 hover:border-gray-300': activeTab !== 'myTickets'}" 
                               class="inline-block p-4">
                                My Tickets
                            </a>
                        </li>
                        <li class="mr-2">
                            <a href="#" @click.prevent="activeTab = 'availableTickets'; fetchAvailableTickets()" 
                               :class="{'border-b-2 border-blue-600 text-blue-600': activeTab === 'availableTickets', 'hover:text-gray-600 hover:border-gray-300': activeTab !== 'availableTickets'}" 
                               class="inline-block p-4">
                                Available Tickets
                            </a>
                        </li>
                        <li class="mr-2">
                            <a href="#" @click.prevent="activeTab = 'createTicket'; resetTicketForm()" 
                               :class="{'border-b-2 border-blue-600 text-blue-600': activeTab === 'createTicket', 'hover:text-gray-600 hover:border-gray-300': activeTab !== 'createTicket'}" 
                               class="inline-block p-4">
                                Create Ticket
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- My Tickets Tab Content -->
                <div x-show="activeTab === 'myTickets'">
                    <h2 class="text-xl font-semibold mb-4">My Tickets</h2>
                    <div x-show="loading" class="text-center py-4">Loading tickets...</div>
                    <div x-show="!loading && tickets.length === 0" class="text-center py-4">No tickets found. Create your first ticket!</div>
                    <div x-show="!loading && tickets.length > 0" class="grid md:grid-cols-2 gap-4">
                        <template x-for="ticket in tickets" :key="ticket.id">
                            <div class="bg-white p-4 rounded shadow">
                                <div class="flex justify-between items-start">
                                    <h3 class="font-semibold text-lg" x-text="ticket.subject"></h3>
                                    <span :class="{
                                        'bg-yellow-200 text-yellow-800': ticket.status === 'open',
                                        'bg-blue-200 text-blue-800': ticket.status === 'in_progress',
                                        'bg-green-200 text-green-800': ticket.status === 'resolved'
                                    }" class="px-2 py-1 rounded text-xs" x-text="ticket.status"></span>
                                </div>
                                <p class="my-2 text-gray-600" x-text="ticket.description"></p>
                                <div class="mt-4 flex justify-between">
                                    <button @click="viewTicket(ticket)" class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded text-sm">
                                        View Details
                                    </button>
                                    <div>
                                        <button @click="editTicket(ticket)" class="bg-yellow-600 hover:bg-yellow-700 text-white px-3 py-1 rounded text-sm mr-2">
                                            Edit
                                        </button>
                                        <button @click="deleteTicket(ticket.id)" class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded text-sm">
                                            Delete
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>

                <!-- Available Tickets Tab Content -->
                <div x-show="activeTab === 'availableTickets'">
                    <h2 class="text-xl font-semibold mb-4">Available Tickets</h2>
                    <div x-show="loadingAvailable" class="text-center py-4">Loading available tickets...</div>
                    <div x-show="!loadingAvailable && availableTickets.length === 0" class="text-center py-4">No available tickets found.</div>
                    <div x-show="!loadingAvailable && availableTickets.length > 0" class="grid md:grid-cols-2 gap-4">
                        <template x-for="ticket in availableTickets" :key="ticket.id">
                            <div class="bg-white p-4 rounded shadow">
                                <div class="flex justify-between items-start">
                                    <h3 class="font-semibold text-lg" x-text="ticket.subject"></h3>
                                    <span class="bg-yellow-200 text-yellow-800 px-2 py-1 rounded text-xs">Open</span>
                                </div>
                                <p class="my-2 text-gray-600" x-text="ticket.description"></p>
                                <div class="mt-4">
                                    <button @click="claimTicket(ticket.id)" class="bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded text-sm">
                                        Claim Ticket
                                    </button>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>

                <!-- Create Ticket Tab Content -->
                <div x-show="activeTab === 'createTicket'">
                    <h2 class="text-xl font-semibold mb-4" x-text="ticketForm.id ? 'Update Ticket' : 'Create New Ticket'"></h2>
                    <div class="bg-white p-6 rounded shadow">
                        <form @submit.prevent="submitTicket">
                            <div class="mb-4">
                                <label class="block mb-1">Subject:</label>
                                <input type="text" x-model="ticketForm.subject" required class="w-full p-2 border rounded">
                            </div>
                            <div class="mb-4">
                                <label class="block mb-1">Description:</label>
                                <textarea x-model="ticketForm.description" required rows="4" class="w-full p-2 border rounded"></textarea>
                            </div>
                            <div x-show="ticketError" class="text-red-600 mb-4" x-text="ticketError"></div>
                            <div class="flex justify-between">
                                <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded" 
                                        x-text="ticketForm.id ? 'Update Ticket' : 'Create Ticket'"></button>
                                <button x-show="ticketForm.id" type="button" @click="resetTicketForm" 
                                        class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded">Cancel</button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Ticket Detail Modal -->
                <div x-show="showTicketDetail" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4">
                    <div class="bg-white rounded-lg shadow-lg w-full max-w-3xl max-h-screen overflow-y-auto" @click.away="showTicketDetail = false">
                        <div class="p-6">
                            <div class="flex justify-between items-start mb-4">
                                <h2 class="text-xl font-semibold" x-text="selectedTicket.subject"></h2>
                                <div class="flex items-center">
                                    <span :class="{
                                        'bg-yellow-200 text-yellow-800': selectedTicket.status === 'open',
                                        'bg-blue-200 text-blue-800': selectedTicket.status === 'in_progress',
                                        'bg-green-200 text-green-800': selectedTicket.status === 'resolved'
                                    }" class="px-2 py-1 rounded text-xs mr-4" x-text="selectedTicket.status"></span>
                                    <button @click="showTicketDetail = false" class="text-gray-500 hover:text-gray-700">
                                        &times;
                                    </button>
                                </div>
                            </div>
                            
                            <div class="mb-6">
                                <h3 class="font-semibold mb-2">Description</h3>
                                <p class="text-gray-700" x-text="selectedTicket.description"></p>
                            </div>
                            
                            <div x-show="selectedTicket.status !== 'resolved' && userData.role === 'agent'" class="mb-6">
                                <button @click="resolveTicket(selectedTicket.id)" class="bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded text-sm">
                                    Mark as Resolved
                                </button>
                            </div>
                            
                            <!-- Responses Section -->
                            <div class="mb-6">
                                <h3 class="font-semibold mb-4">Responses</h3>
                                <div x-show="loadingResponses" class="text-center py-4">Loading responses...</div>
                                <div x-show="!loadingResponses && responses.length === 0" class="text-center py-4 text-gray-500">No responses yet.</div>
                                <div x-show="!loadingResponses && responses.length > 0" class="space-y-4">
                                    <template x-for="response in responses" :key="response.id">
                                        <div class="bg-gray-100 p-4 rounded">
                                            <div class="flex justify-between items-start mb-2">
                                                <span class="font-semibold" x-text="response.agent_name || 'Agent'"></span>
                                                <span class="text-xs text-gray-500" x-text="formatDate(response.created_at)"></span>
                                            </div>
                                            <p class="text-gray-700" x-text="response.content"></p>
                                        </div>
                                    </template>
                                </div>
                            </div>
                            
                            <!-- Add Response Form (only for agents) -->
                            <div x-show="userData.role === 'agent'">
                                <h3 class="font-semibold mb-2">Add Response</h3>
                                <form @submit.prevent="addResponse">
                                    <div class="mb-4">
                                        <textarea x-model="responseForm.content" required rows="3" class="w-full p-2 border rounded" 
                                                 placeholder="Type your response here..."></textarea>
                                    </div>
                                    <div x-show="responseError" class="text-red-600 mb-4" x-text="responseError"></div>
                                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">
                                        Submit Response
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function app() {
            const API_URL = 'http://localhost:8000/api'; // Change this to your API URL
            
            return {
                isAuthenticated: false,
                userData: {},
                authToken: '',
                loginForm: {
                    email: '',
                    password: ''
                },
                registerForm: {
                    name: '',
                    email: '',
                    password: '',
                    password_confirmation: '',
                    agent_code: ''
                },
                loginError: '',
                registerError: '',
                tickets: [],
                availableTickets: [],
                loading: false,
                loadingAvailable: false,
                loadingResponses: false,
                activeTab: 'myTickets',
                ticketForm: {
                    id: null,
                    subject: '',
                    description: ''
                },
                ticketError: '',
                showTicketDetail: false,
                selectedTicket: {},
                responses: [],
                responseForm: {
                    content: ''
                },
                responseError: '',
                
                init() {
                    // Check for existing auth token in local storage
                    const token = localStorage.getItem('auth_token');
                    const user = localStorage.getItem('user_data');
                    
                    if (token && user) {
                        this.authToken = token;
                        this.userData = JSON.parse(user);
                        this.isAuthenticated = true;
                        this.fetchTickets();
                    }
                },
                
                // Authentication Methods
                async login() {
                    this.loginError = '';
                    try {
                        const response = await fetch(`${API_URL}/login`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify(this.loginForm)
                        });
                        
                        const data = await response.json();
                        
                        if (!response.ok) {
                            this.loginError = data.message || 'Login failed. Please check your credentials.';
                            return;
                        }
                        
                        // Store auth token and user data
                        this.authToken = data.token;
                        this.userData = data.user;
                        this.isAuthenticated = true;
                        
                        localStorage.setItem('auth_token', data.token);
                        localStorage.setItem('user_data', JSON.stringify(data.user));
                        
                        // Reset form
                        this.loginForm = { email: '', password: '' };
                        
                        // Fetch tickets
                        this.fetchTickets();
                        
                    } catch (error) {
                        this.loginError = 'An error occurred during login. Please try again.';
                        console.error('Login error:', error);
                    }
                },
                
                async register() {
                    this.registerError = '';
                    try {
                        const response = await fetch(`${API_URL}/register`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify(this.registerForm)
                        });
                        
                        const data = await response.json();
                        
                        if (!response.ok) {
                            this.registerError = data.message || 'Registration failed. Please check your input.';
                            return;
                        }
                        
                        // Store auth token and user data
                        this.authToken = data.token;
                        this.userData = data.user;
                        this.isAuthenticated = true;
                        
                        localStorage.setItem('auth_token', data.token);
                        localStorage.setItem('user_data', JSON.stringify(data.user));
                        
                        // Reset form
                        this.registerForm = { 
                            name: '',
                            email: '',
                            password: '',
                            password_confirmation: '',
                            agent_code: ''
                        };
                        
                        // Fetch tickets
                        this.fetchTickets();
                        
                    } catch (error) {
                        this.registerError = 'An error occurred during registration. Please try again.';
                        console.error('Registration error:', error);
                    }
                },
                
                async logout() {
                    try {
                        await fetch(`${API_URL}/logout`, {
                            method: 'POST',
                            headers: {
                                'Authorization': `Bearer ${this.authToken}`,
                                'Accept': 'application/json'
                            }
                        });
                    } catch (error) {
                        console.error('Logout error:', error);
                    } finally {
                        // Clear auth data regardless of API response
                        this.authToken = '';
                        this.userData = {};
                        this.isAuthenticated = false;
                        this.tickets = [];
                        
                        localStorage.removeItem('auth_token');
                        localStorage.removeItem('user_data');
                    }
                },
                
                // Ticket Methods
                async fetchTickets() {
                    if (!this.isAuthenticated) return;
                    
                    this.loading = true;
                    try {
                        const response = await fetch(`${API_URL}/tickets`, {
                            headers: {
                                'Authorization': `Bearer ${this.authToken}`,
                                'Accept': 'application/json'
                            }
                        });
                        
                        if (!response.ok) {
                            throw new Error('Failed to fetch tickets');
                        }
                        
                        const data = await response.json();
                        this.tickets = data;
                        
                    } catch (error) {
                        console.error('Error fetching tickets:', error);
                    } finally {
                        this.loading = false;
                    }
                },
                
                async fetchAvailableTickets() {
                    if (!this.isAuthenticated || this.userData.role !== 'agent') return;
                    
                    this.loadingAvailable = true;
                    try {
                        const response = await fetch(`${API_URL}/tickets/available`, {
                            headers: {
                                'Authorization': `Bearer ${this.authToken}`,
                                'Accept': 'application/json'
                            }
                        });
                        
                        if (!response.ok) {
                            throw new Error('Failed to fetch available tickets');
                        }
                        
                        const data = await response.json();
                        this.availableTickets = data;
                        
                    } catch (error) {
                        console.error('Error fetching available tickets:', error);
                    } finally {
                        this.loadingAvailable = false;
                    }
                },
                
                resetTicketForm() {
                    this.ticketForm = {
                        id: null,
                        subject: '',
                        description: ''
                    };
                    this.ticketError = '';
                },
                
                editTicket(ticket) {
                    this.ticketForm = {
                        id: ticket.id,
                        subject: ticket.subject,
                        description: ticket.description
                    };
                    this.activeTab = 'createTicket';
                },
                
                async submitTicket() {
                    this.ticketError = '';
                    
                    try {
                        const isUpdate = !!this.ticketForm.id;
                        const url = isUpdate ? 
                            `${API_URL}/tickets/${this.ticketForm.id}` : 
                            `${API_URL}/tickets`;
                        const method = isUpdate ? 'PUT' : 'POST';
                        
                        const response = await fetch(url, {
                            method,
                            headers: {
                                'Authorization': `Bearer ${this.authToken}`,
                                'Content-Type': 'application/json',
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify(this.ticketForm)
                        });
                        
                        if (!response.ok) {
                            const errorData = await response.json();
                            this.ticketError = errorData.message || 'Failed to save ticket';
                            return;
                        }
                        
                        // Reset form and refresh tickets
                        this.resetTicketForm();
                        this.fetchTickets();
                        this.activeTab = 'myTickets';
                        
                    } catch (error) {
                        this.ticketError = 'An error occurred while saving the ticket';
                        console.error('Error saving ticket:', error);
                    }
                },
                
                async deleteTicket(ticketId) {
                    if (!confirm('Are you sure you want to delete this ticket?')) return;
                    
                    try {
                        const response = await fetch(`${API_URL}/tickets/${ticketId}`, {
                            method: 'DELETE',
                            headers: {
                                'Authorization': `Bearer ${this.authToken}`,
                                'Accept': 'application/json'
                            }
                        });
                        
                        if (!response.ok) {
                            throw new Error('Failed to delete ticket');
                        }
                        
                        // Refresh tickets
                        this.fetchTickets();
                        
                    } catch (error) {
                        console.error('Error deleting ticket:', error);
                        alert('Failed to delete ticket. Please try again.');
                    }
                },
                
                async claimTicket(ticketId) {
                    if (this.userData.role !== 'agent') {
                        alert('Only agents can claim tickets');
                        return;
                    }
                    
                    try {
                        const response = await fetch(`${API_URL}/tickets/${ticketId}/claim`, {
                            method: 'POST',
                            headers: {
                                'Authorization': `Bearer ${this.authToken}`,
                                'Accept': 'application/json'
                            }
                        });
                        
                        if (!response.ok) {
                            throw new Error('Failed to claim ticket');
                        }
                        
                        // Refresh ticket lists
                        this.fetchAvailableTickets();
                        this.fetchTickets();
                        
                    } catch (error) {
                        console.error('Error claiming ticket:', error);
                        alert('Failed to claim ticket. Please try again.');
                    }
                },
                
                async resolveTicket(ticketId) {
                    if (this.userData.role !== 'agent') {
                        alert('Only agents can resolve tickets');
                        return;
                    }
                    
                    try {
                        const response = await fetch(`${API_URL}/tickets/${ticketId}/resolve`, {
                            method: 'PATCH',
                            headers: {
                                'Authorization': `Bearer ${this.authToken}`,
                                'Accept': 'application/json'
                            }
                        });
                        
                        if (!response.ok) {
                            throw new Error('Failed to resolve ticket');
                        }
                        
                        // Update the ticket in the current view
                        if (this.showTicketDetail && this.selectedTicket.id === ticketId) {
                            this.selectedTicket.status = 'resolved';
                        }
                        
                        // Refresh tickets
                        this.fetchTickets();
                        
                    } catch (error) {
                        console.error('Error resolving ticket:', error);
                        alert('Failed to resolve ticket. Please try again.');
                    }
                },
                
                async viewTicket(ticket) {
                    this.selectedTicket = { ...ticket };
                    this.showTicketDetail = true;
                    this.responses = [];
                    this.responseForm.content = '';
                    this.responseError = '';
                    
                    // Fetch responses
                    await this.fetchResponses(ticket.id);
                },
                
                // Response Methods
                async fetchResponses(ticketId) {
                    this.loadingResponses = true;
                    try {
                        const response = await fetch(`${API_URL}/tickets/${ticketId}/responses`, {
                            headers: {
                                'Authorization': `Bearer ${this.authToken}`,
                                'Accept': 'application/json'
                            }
                        });
                        
                        if (!response.ok) {
                            throw new Error('Failed to fetch responses');
                        }
                        
                        const data = await response.json();
                        this.responses = data;
                        
                    } catch (error) {
                        console.error('Error fetching responses:', error);
                    } finally {
                        this.loadingResponses = false;
                    }
                },
                
                async addResponse() {
                    if (this.userData.role !== 'agent') {
                        alert('Only agents can add responses');
                        return;
                    }
                    
                    this.responseError = '';
                    
                    try {
                        const response = await fetch(`${API_URL}/tickets/${this.selectedTicket.id}/responses`, {
                            method: 'POST',
                            headers: {
                                'Authorization': `Bearer ${this.authToken}`,
                                'Content-Type': 'application/json',
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify(this.responseForm)
                        });
                        
                        if (!response.ok) {
                            const errorData = await response.json();
                            this.responseError = errorData.message || 'Failed to add response';
                            return;
                        }
                        
                        // Reset form and refresh responses
                        this.responseForm.content = '';
                        this.fetchResponses(this.selectedTicket.id);
                        
                    } catch (error) {
                        this.responseError = 'An error occurred while adding the response';
                        console.error('Error adding response:', error);
                    }
                },
                
                // Helper Methods
                formatDate(dateString) {
                    if (!dateString) return '';
                    const date = new Date(dateString);
                    return date.toLocaleString();
                }
            };
        }
    </script>
</body>
</html>