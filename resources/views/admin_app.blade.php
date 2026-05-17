<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Admin Panel</title>
    <script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <style>
        body{font-family:Arial;margin:40px;background:#f4f7f6}
        .card{background:white;padding:20px;border-radius:8px;box-shadow:0 2px 5px rgba(0,0,0,0.1);margin-bottom:20px}
        button{padding:10px 15px;cursor:pointer;border-radius:4px;border:1px solid #ccc;margin-right:5px}
        .btn-approve{background:#28a745;color:white;border:none}
        .btn-reject{background:#dc3545;color:white;border:none}
        .status{font-weight:bold;color:#555;background:#eee;padding:2px 8px;border-radius:10px;font-size:12px}
        input{display:block;margin:10px 0;padding:10px;width:250px}
    </style>
</head>
<body>

@verbatim
<div id="app">
    <h2>Admin Management</h2>

    <div v-if="!isLoggedIn">
        <div class="card">
            <h3>Admin Login</h3>
            <input v-model="login.email" placeholder="Email">
            <input type="password" v-model="login.password" placeholder="Password">
            <button @click="doLogin">Login</button>
        </div>
    </div>

    <div v-else>
        <div style="display:flex; justify-content: space-between; align-items: center">
            <p>Role: <strong>{{ role }}</strong></p>
            <button @click="doLogout">Logout</button>
        </div>
        <hr>

        <p v-if="loading">Loading tickets...</p>

        <div v-for="ticket in tickets" :key="ticket.id" class="card">
            <h3>{{ ticket.title }}</h3>
            <p>{{ ticket.description }}</p>
            <p>Current Status: <span class="status">{{ ticket.state }}</span></p>

            <!-- Buttons will appear if this function returns true -->
            <div v-if="canAction(ticket.state)" style="margin-top:15px">
                <button class="btn-approve" @click="handleAction(ticket.id, 'approve')">Approve Ticket</button>
                <button class="btn-reject" @click="handleAction(ticket.id, 'reject')">Reject Ticket</button>
            </div>
        </div>

        <p v-if="!loading && tickets.length === 0">No pending tickets found.</p>
    </div>
</div>
@endverbatim

<script>
    axios.defaults.withCredentials = true;
    axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

    const { createApp } = Vue;

    createApp({
        data() {
            return {
                isLoggedIn: false,
                role: '',
                loading: false,
                tickets: [],
                login: { email: '', password: '' }
            }
        },
        methods: {
            async doLogin() {
                try {
                    await axios.get('/sanctum/csrf-cookie');
                    await axios.post('/api/auth/login', this.login);
                    await this.checkUser();
                } catch (e) { alert('Auth Failed'); }
            },
            async doLogout() {
                await axios.post('/api/auth/logout');
                this.isLoggedIn = false;
                this.tickets = [];
            },
            async checkUser() {
                try {
                    const res = await axios.get('/api/user');
                    this.role = res.data.role;
                    this.isLoggedIn = true;
                    this.loadTickets();
                } catch (e) { this.isLoggedIn = false; }
            },
            async loadTickets() {
                this.loading = true;
                // Normalize role check for admin_1 or admin1
                const type = this.role.includes('1') ? 'admin1' : 'admin2';
                const res = await axios.get(`/api/admin/tickets/${type}`);
                this.tickets = res.data.data || res.data;
                this.loading = false;
            },
            async handleAction(id, actionType) {
                const typeSuffix = this.role.includes('1') ? 'admin-1' : 'admin-2';
                const url = `/api/admin/tickets/${id}/${actionType}-${typeSuffix}`;
                try {
                    await axios.post(url);
                    await this.loadTickets();
                } catch (e) { alert('Action failed'); }
            },
            canAction(state) {
                if (!state) return false;

                const s = state.toLowerCase();

                // Admin 1 can modify any ticket that hasn't reached admin2 stage yet
                if (this.role.includes('1')) {
                    return (
                        s === 'submitted' ||
                        s === 'approved_by_admin1' ||
                        s === 'rejected_by_admin1'
                    );
                }

                // Admin 2 can modify tickets after admin1 decision
                if (this.role.includes('2')) {
                    return (
                        s === 'approved_by_admin1' ||
                        s === 'rejected_by_admin1' ||
                        s === 'approved_by_admin2' ||
                        s === 'rejected_by_admin2'
                    );
                }

                return false;
            }
        },
        mounted() {
            this.checkUser();
        }
    }).mount('#app');
</script>
</body>
</html>
