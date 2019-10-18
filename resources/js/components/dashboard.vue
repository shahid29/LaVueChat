<template>
        <div class="container-chat clearfix">
            <div class="people-list" id="people-list">
                <div class="search">
                    <input type="text" placeholder="search" />
                    <i class="fa fa-search"></i>
                </div>
                <ul class="list">

                    <li @click.prevent="userMessage(user.id)" class="clearfix" v-for="user in userList" :key="user.id">

                        <div class="about">
                            <div class="name">{{user.name}}</div>
                            <div class="status" style="color:#fff">
                                <div v-if="onlinUser(user.id) || online.id==user.id ">
                                    <i  class="fa fa-circle online"></i> online
                                </div>
                                <div v-else="">
                                    <i  class="fa fa-circle"></i> offline
                                </div>

                            </div>
                        </div>
                    </li>


                </ul>
            </div>

            <div  class="chat">
                <div v-if="userMessages.user"  class="chat-header clearfix">
                    <img src="https://s3-us-west-2.amazonaws.com/s.cdpn.io/195612/chat_avatar_01_green.jpg" alt="avatar" />

                    <div class="chat-about">
                        <div v-if="userMessages.user" class="chat-with">{{userMessages.user.name}}</div>

                    </div>
                    <i class="fa fa-star"></i><ul class="nav nav-tabs">

                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">...</a>
                        <div class="dropdown-menu">
                            <a v-if="userMessages.user" @click.prevent="deleteMessageAll(userMessages.user.id)" class="dropdown-item" href="#">Delete All Message</a>
                        </div>
                    </li>

                </ul>
                </div> <!-- end chat-header -->

                <div class="chat-history" v-chat-scroll="{always: true, smooth: true, scrollonremoved:true, smoothonremoved: false}">
                    <ul>

                        <li v-if="userMessages.user" v-for="message in userMessages.userMessage" class="clearfix" :key="message.id" >
                            <div class="message-data align-right">
                                <span class="message-data-time" >{{message.created_at|timeFormat()}}</span> &nbsp; &nbsp;
                                <span class="message-data-name" >{{message.user.name}}</span><i class="fa fa-circle me"></i>
                                <ul class="nav nav-tabs">

                                    <li class="nav-item dropdown">
                                        <a class="nav-link dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">...</a>
                                        <div class="dropdown-menu">
                                            <a @click.prevent="deleteMessage(message.id)" class="dropdown-item" href="#">Delete Message</a>
                                        </div>
                                    </li>

                                </ul>
                            </div>
                            <div :class="`message float-right ${message.user.id==userMessages.user.id?'other-message':'my-message'}`">
                                {{message.message}}
                            </div>
                        </li>

                    </ul>

                </div> <!-- end chat-history -->

                <div v-if="userMessages.user"  class="chat-message clearfix">
                    <p v-if="typing">{{typing}} typing.......</p>
                    <textarea @keydown="typingEvent(userMessages.user.id)" @keydown.enter.prevent="sendMessage" v-model="message" placeholder ="Type your message" rows="3"></textarea>
                    <button @click.prevent="sendMessage">Send</button>

                </div>
                <div v-else="" hidden class="chat-message clearfix">
                    <p > typing.......</p>
                    <textarea @keydown.enter.prevent="sendMessage" v-model="message" placeholder ="Type your message" rows="3"></textarea>
                    <i class="fa fa-file-o"></i> &nbsp;&nbsp;&nbsp;
                    <i class="fa fa-file-image-o"></i>
                    <button>Send</button>

                </div><!-- end chat-message -->

            </div> <!-- end chat -->

        </div> <!-- end container -->
    </template>

<script>
    import _ from 'lodash'
    export default {

        name: "dashboard",
        mounted(){
            this.$store.dispatch('userList')

            Echo.private(`chat.${authuser.id}`)
                .listen('SendMessage', (e) => {
                   this.userMessage(e.message.from)
                });

            Echo.private('typingEvent')
                .listenForWhisper('typing', (e) => {
                    if (e.user.id== this.userMessages.user.id && e.userId == authuser.id){
                        this.typing = e.user.name
                    }


                    setTimeout(()=>{
                        this.typing =''
                    },5000);
                });

            Echo.join('liveuser')
                .here((users) => {
                this.users=users
                })
                .joining((user) => {
                    this.online=user
                })
                .leaving((user) => {
                    this.users=user
                });
        },
        computed:{
            userList(){
               return this.$store.getters.userList
            },
            userMessages(){
                return this.$store.getters.userMessage
            },


        },
        data(){
            return{
                message:'',
                typing:'',
                users:[],
                online:''
            }
        },

        created(){

        },
        methods:{
            userMessage(id){
                this.$store.dispatch('userMessage',id)
            },

            sendMessage(){
                if (this.message !=''){
                    axios.post('/sendmessage',{message:this.message,user_id:this.userMessages.user.id})
                        .then(response=>{
                            this.userMessage(this.userMessages.user.id)
                        })
                    this.message=''
                }
            },

            deleteMessageAll(id){
                Swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    type: 'warning',
                    showCancelButton: true,
                    cancelButtonText: 'No, cancel!',
                    cancelButtonColor: '#d33',
                    confirmButtonColor: '#3085d6',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.dismiss === Swal.DismissReason.cancel){

                    }else{
                        axios.get('/deleteMessageAll/'+id)
                            .then(()=>{
                            this.userMessage(this.userMessages.user.id)
                            if (result.value) {
                                Swal.fire(
                                    'Deleted!',
                                    'Messages has been deleted.',
                                    'success'
                                )
                            }
                        })
                    }


                })


            },

            deleteMessage(id){
                Swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    type: 'warning',
                    showCancelButton: true,
                    cancelButtonText: 'No, cancel!',
                    cancelButtonColor: '#d33',
                    confirmButtonColor: '#3085d6',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.dismiss === Swal.DismissReason.cancel){

                    }else{
                        axios.get('/deleteMessage/'+id)
                            .then(()=>{
                                this.userMessage(this.userMessages.user.id)
                                if (result.value) {
                                    Swal.fire(
                                        'Deleted!',
                                        'Your Selected Message has been deleted.',
                                        'success'
                                    )
                                }
                            })
                    }


                })


            },

            typingEvent(id){
                Echo.private('typingEvent')
                    .whisper('typing', {
                        'user': authuser,
                        'typing':this.message,
                        'userId':id,
                    });
            },

            onlinUser(id){
                return _.find(this.users,{'id':id})
            }
        }
    }
</script>

<style scoped>

</style>
