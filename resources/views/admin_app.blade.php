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
button{padding:8px 12px;border-radius:4px;border:none;margin-right:5px;cursor:pointer}
.btn-approve{background:#28a745;color:white}
.btn-reject{background:#dc3545;color:white}
.status{font-weight:bold;background:#eee;padding:3px 8px;border-radius:10px;font-size:12px}
input,textarea{padding:8px;width:100%;margin-top:6px}
.bulk-bar{margin-bottom:20px}
</style>
</head>

<body>

@verbatim
<div id="app">

<h2>Admin Panel</h2>

<div v-if="!isLoggedIn" class="card">
<h3>Login</h3>
<input v-model="login.email" placeholder="Email">
<input type="password" v-model="login.password" placeholder="Password">
<button @click="doLogin">Login</button>
</div>

<div v-else>

<div style="display:flex;justify-content:space-between">
<p>Role: <strong>{{ role }}</strong></p>
<button @click="doLogout">Logout</button>
</div>

<hr>

<div class="bulk-bar" v-if="tickets.length">
<button class="btn-approve" @click="bulkAction('approve')">Bulk Approve</button>
<button class="btn-reject" @click="bulkAction('reject')">Bulk Reject</button>
</div>

<p v-if="loading">Loading tickets...</p>

<div v-for="ticket in tickets" :key="ticket.id" class="card">

<input type="checkbox" v-model="selected" :value="ticket.id">

<h3>{{ ticket.title }}</h3>

<p>{{ ticket.description }}</p>

<p>Status:
<span class="status">{{ ticket.state }}</span>
</p>

<p v-if="ticket.attachment">
<a :href="'/storage/' + ticket.attachment" target="_blank">
View Attachment
</a>
</p>

<textarea v-model="ticket.commentInput"
placeholder="Write comment..."></textarea>

<div style="margin-top:10px">
<button class="btn-approve"
@click="handleAction(ticket,'approve')">
Approve
</button>

<button class="btn-reject"
@click="handleAction(ticket,'reject')">
Reject
</button>
</div>

<p v-if="ticket.admin1_comment">
<strong>Admin1:</strong> {{ ticket.admin1_comment }}
</p>

<p v-if="ticket.admin2_comment">
<strong>Admin2:</strong> {{ ticket.admin2_comment }}
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
isLoggedIn:false,
role:'',
loading:false,
tickets:[],
selected:[],
login:{email:'',password:''}
}
},

methods:{

async doLogin(){
await axios.get('/sanctum/csrf-cookie')
await axios.post('/api/auth/login_admin',this.login)
this.checkUser()
},

async doLogout(){
await axios.post('/api/auth/logout')
this.isLoggedIn=false
this.tickets=[]
},

async checkUser(){
const res = await axios.get('/api/user')
this.role=res.data.role
this.isLoggedIn=true
this.loadTickets()
},

async loadTickets(){
this.loading=true
const type=this.role.includes('1')?'admin1':'admin2'
const res=await axios.get(`/api/admin/tickets/${type}`)
this.tickets=(res.data.data||res.data).map(t=>{
t.commentInput=''
return t
})
this.loading=false
},

async handleAction(ticket,action){

const suffix=this.role.includes('1')?'admin-1':'admin-2'
const url=`/api/admin/tickets/${ticket.id}/${action}-${suffix}`

await axios.post(url,{
comment:ticket.commentInput
})

ticket.commentInput=''
this.loadTickets()
},

async bulkAction(action){

if(this.selected.length===0){
alert("Select tickets first")
return
}

const comment=prompt("Enter bulk comment")

const type=this.role.includes('1')?'admin-1':'admin-2'

await axios.post(`/api/admin/tickets/bulk-${action}-${type}`,{
ticket_ids:this.selected,
comment:comment
})

this.selected=[]
this.loadTickets()
}

},

mounted(){
this.checkUser()
}

}).mount('#app')

</script>

</body>
</html>
