<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>User Panel</title>

<script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

<style>
body{font-family:Arial;margin:40px}
input,textarea{display:block;margin:8px 0;padding:6px;width:300px}
button{padding:6px 12px;margin:6px 0}
.ticket{border:1px solid #ddd;padding:12px;margin:10px 0}
.loading{color:#777}
</style>
</head>

<body>

@verbatim
<div id="app">

<h2>User Panel</h2>

<div v-if="page==='login'">

<h3>Login</h3>

<input v-model="login.email" placeholder="Email">
<input type="password" v-model="login.password" placeholder="Password">

<button @click="loginUser">Login</button>

<p>
<a href="#" @click.prevent="page='register'">Register</a>
</p>

</div>


<div v-if="page==='register'">

<h3>Register</h3>

<input v-model="register.name" placeholder="Name">
<input v-model="register.email" placeholder="Email">
<input type="password" v-model="register.password" placeholder="Password">

<button @click="registerUser">Register</button>

<p>
<a href="#" @click.prevent="page='login'">Back</a>
</p>

</div>


<div v-if="page==='tickets'">

<button @click="logout">Logout</button>

<h3>Create Ticket</h3>

<input v-model="form.title" placeholder="Title">

<textarea v-model="form.description" placeholder="Description"></textarea>

<button @click="createTicket">Create</button>

<hr>

<h3>Your Tickets</h3>

<p v-if="loading" class="loading">Loading...</p>

<div v-for="ticket in tickets" :key="ticket.id" class="ticket">

<strong>{{ ticket.title }}</strong>

<p>{{ ticket.description }}</p>

<p>Status: {{ ticket.state }}</p>

</div>

</div>

</div>
@endverbatim


<script>

axios.defaults.withCredentials = true
axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest'

const { createApp } = Vue

createApp({

data(){
return{
page:'login',
loading:false,
tickets:[],
login:{email:'',password:''},
register:{name:'',email:'',password:''},
form:{title:'',description:''}
}
},

methods:{

async loginUser(){

await axios.get('/sanctum/csrf-cookie')

await axios.post('/api/auth/login',this.login)

this.page='tickets'

this.loadTickets()

},

async registerUser(){

await axios.get('/sanctum/csrf-cookie')

await axios.post('/api/auth/register',this.register)

this.page='login'

},

async logout(){

await axios.post('/api/auth/logout')

this.page='login'
this.tickets=[]

},

async loadTickets(){

this.loading=true

const res = await axios.get('/api/tickets')

this.tickets = res.data.data ?? res.data

this.loading=false

},

async createTicket(){

await axios.post('/api/tickets',this.form)

this.form.title=''
this.form.description=''

await this.loadTickets()

}

},

async mounted(){

try{

const user = await axios.get('/api/user')

if(user.data){

this.page='tickets'
this.loadTickets()

}

}catch{}

}

}).mount('#app')

</script>

</body>
</html>
