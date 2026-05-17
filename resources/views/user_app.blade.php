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
.ticket{border:1px solid #ddd;padding:12px;margin:10px 0;border-radius:6px}
.loading{color:#777}
a{color:blue}
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
<p><a href="#" @click.prevent="page='register'">Register</a></p>
</div>

<div v-if="page==='register'">
<h3>Register</h3>
<input v-model="register.name" placeholder="Name">
<input v-model="register.email" placeholder="Email">
<input type="password" v-model="register.password" placeholder="Password">
<button @click="registerUser">Register</button>
<p><a href="#" @click.prevent="page='login'">Back</a></p>
</div>

<div v-if="page==='tickets'">

<button @click="logout">Logout</button>

<h3>Create Ticket</h3>

<input v-model="form.title" placeholder="Title">
<textarea v-model="form.description" placeholder="Description"></textarea>

<label>Attach File (optional)</label>
<input type="file" @change="handleFile">

<button @click="createTicket">Create</button>

<hr>

<h3>Your Tickets</h3>

<p v-if="loading" class="loading">Loading...</p>

<div v-for="ticket in tickets" :key="ticket.id" class="ticket">

<strong>{{ ticket.title }}</strong>

<p>{{ ticket.description }}</p>

<p>Status: {{ ticket.state }}</p>

<p v-if="ticket.admin1_comment">
<strong>Admin 1 Comment:</strong> {{ ticket.admin1_comment }}
</p>

<p v-if="ticket.admin2_comment">
<strong>Admin 2 Comment:</strong> {{ ticket.admin2_comment }}
</p>

<p v-if="ticket.attachment">
<a :href="'/storage/' + ticket.attachment" target="_blank">View Attachment</a>
</p>

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
file:null,
login:{email:'',password:''},
register:{name:'',email:'',password:''},
form:{title:'',description:''}
}
},

methods:{

handleFile(e){
this.file = e.target.files[0]
},

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

let formData = new FormData()
formData.append('title',this.form.title)
formData.append('description',this.form.description)

if(this.file){
formData.append('attachment_path',this.file)
}

await axios.post('/api/tickets',formData,{
headers:{'Content-Type':'multipart/form-data'}
})

this.form.title=''
this.form.description=''
this.file=null

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
