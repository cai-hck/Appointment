<template>
    <div class="w-100">
        <div class="chat-window">        
            <!-- Chat Left -->
            <div class="chat-cont-left" style="min-height:800px">
                <div class="chat-header">
                    <span>{{ lang=='en'?'Mission Users':'مستخدمو المهمة'}}</span>
                </div>								
                <div class="chat-users-list">
                    <div class="chat-scroll">
                        <a v-for="(consul,ckey) in consuls" :key="'c'+ckey" href="javascript:void(0);" class="media"
                            :class="selected_user.uid==consul.user_id?'bg-success-light':''"
                            :data-uid="consul.user_id">
                            <div class="media-img-wrap">
                                <div class="avatar avatar-away">
                                    <img :src="url + '/' + JSON.parse(consul.photo)['s']" alt="User Image" class="avatar-img rounded-circle">
                                </div>
                            </div>
                            <div class="media-body">
                                <div>
                                    <div class="user-name">{{consul.fname + ' ' + consul.lname}}</div>
                                    <div class="user-last-chat text-muted"><mark>{{lang=='en'?'Consultant':'مستشار'}}</mark></div>
                                </div>
                                <div>
                                    <div v-if="selected_user.uid!=consul.user_id && unread[ckey].val!=0"
                                        class="badge badge-success badge-pill">
                                        {{unread[ckey].val}}
                                    </div>
                                </div>
                            </div>
                        </a>
                        <a v-for="(secret,skey) in secrets" :key="'s'+skey" href="javascript:void(0);" class="media"
                            :class="selected_user.uid==secret.user_id?'bg-success-light':''"
                             :data-uid="secret.user_id"
                            >
                            <div class="media-img-wrap">
                                <div class="avatar avatar-away">
                                    <img :src="url + '/'+JSON.parse(secret.photo)['s']" alt="User Image" class="avatar-img rounded-circle">
                                </div>
                            </div>
                            <div class="media-body">
                                <div>
                                    <div class="user-name">{{secret.fname + ' ' + secret.lname}}</div>
                                    <div class="user-last-chat text-muted"><mark>{{lang=='en'?'Secretary':'سكرتير'}}</mark></div>
                                </div>
                                <div>
                                    <div v-if="selected_user.uid!=secret.user_id && unread[skey].val!=0"
                                        class="badge badge-success badge-pill">
                                        {{unread[skey].val}}
                                    </div>
                                </div>
                            </div>
                        </a>                                  
                    </div>
                </div>
            </div>
            <!-- /Chat Left -->
        
            <!-- Chat Right -->
            <div class="chat-cont-right" style="min-height:800px">
                <div class="chat-header">
                    <a id="back_user_list" href="javascript:void(0)" class="back-user-list">
                        <i class="material-icons">chevron_left</i>
                    </a>
                    <div class="media">
                        <div class="media-img-wrap">
                            <div class="avatar avatar-online">
                                <img :src="url + '/'+JSON.parse(selected_user.photo)['s']" alt="User Image" class="avatar-img rounded-circle">
                            </div>
                        </div>
                        <div class="media-body">
                            <div class="user-name">{{selected_user.fname + ' ' + selected_user.lname}}</div>
                            <div class="user-status">
                                <span class="text-muted" v-if="activeUser" ><i> {{ lang =='en'?'typing...':'الكتابة ..'}}</i></span>
                            </div>
                        </div>
                    </div>
                    <div class="chat-options">
                        <a v-if="!videocall" href="javascript:void(0)" data-toggle="modal" data-target="#video_call">
                            <i class="material-icons">videocam</i>
                        </a>
                        <a v-if="!zoom_flag" href="javascript:void(0)" @click="zoomIn">
                            <i class="material-icons">zoom_in</i>
                        </a>
                    </div>
                </div>
                <div class="chat-body">


                    <!-- Video Call Wrapper -->
                    <div v-if="videocall" class="call-wrapper video-call-wrapper" :style="full_style">
                        <div class="call-main-row">
                            <div class="call-main-wrapper">
                                <div class="call-view">
                                    <div class="call-window" :style="{'display':videoZoomInOut}">                                                                                                    
                                        <!-- Call Contents -->
                                        <div class="call-contents">
                                            <div class="call-content-wrap">
                                                <div class="user-video" id="user-video">
                                                </div>
                                                <div class="my-video" id="my-video"></div>
                                            </div>
                                        </div>
                                        <!-- Call Contents -->                            
                                        <!-- Call Footer -->
                                        <div class="call-footer">
                                            <div class="call-icons">
                                                <!-- <span class="call-duration">00:59</span> -->
                                                <ul class="call-items">                                                                                 
                                                    <li class="call-item">
                                                        <a href="javascript:void(0);" title="Full Screen" data-placement="top" @click="fullScreen">
                                                            <i class="fa fa-expand full-screen"></i>
                                                        </a>
                                                    </li>
                                                    <li class="call-item">
                                                        <a href="javascript:void(0);" id="video-zoom-out-btn" @click="zoomOut">
                                                            <i class="fa fa-search-minus full-screen"></i>
                                                        </a>
                                                    </li>                                        
                                                </ul>
                                                <div class="end-call">
                                                    <a href="javascript:void(0);" id="video-end-btn" @click="endCall">
                                                        <i class="material-icons">call_end</i>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>                    
                            </div>
                        </div>
                    </div>
                    <!-- /Video Call Wrapper -->


                    <div class="chat-scroll" v-chat-scroll>
                        <ul class="list-unstyled">
                            <li class="media"
                                v-for="(message,index) in messages" :key="'m'+index"
                                :class="setMessagePosition(message.from, message.to)">
                                <div v-if="message.from==selected_user.uid" class="avatar">
                                    <img :src="url + '/'+JSON.parse(selected_user.photo)['s']" alt="User Image" class="avatar-img rounded-circle">
                                </div> 
                                <div class="media-body">
                                    <div class="msg-box">
                                         <div>
                                            <p>{{ message.message }}</p>
                                            <ul class="chat-msg-info">
                                                <li>
                                                    <div class="chat-time">
                                                        <Timeago :datetime="message.created_at" :auto-update="10" :locale="lang"></Timeago>
                                                    </div>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="chat-footer">
                    <div class="input-group">
                        <!-- 
                        <div class="input-group-prepend">
                            <div class="btn-file btn">
                                <i class="fa fa-paperclip"></i>
                                <input type="file">
                            </div>
                        </div>
                        -->
                        <input type="text" class="input-msg-send form-control"
                            v-bind:placeholderplaceholder="lang=='en'?'Type something':'اطبع شيئا'"
                            v-model = "newMessage"
                            @keyup.enter="sendMessage"
                            @keydown="sendTypingEvent"                             
                        >
                        <div class="input-group-append">
                            <button type="button" class="btn msg-send-btn" @click="sendMessage"><i class="fab fa-telegram-plane"></i></button>
                        </div>
                    </div>
                </div>
                <div v-if="callrequest" class="chat-footer">
                    <div class="form-group text-center">
                        <h4>Video calling from {{selected_user.fname + ' ' + selected_user.lname}} </h4>
                        <a href="#" class="btn btn-danger btn-sm ml-2 mr-2" @click="declineCall">{{lang=='en'?'Decline':'انخفاض'}}</a>
                        <a href="#" class="btn btn-success btn-sm ml-2 mr-2" @click="acceptCall">{{ lang=='en'?'Accept':'قبول'}}</a>
                    </div>
                 </div>
            </div>
            <!-- /Chat Right -->            
        </div>

        <!-- Video Call Modal -->
		<div class="modal fade call-modal" id="video_call">
			<div class="modal-dialog modal-dialog-centered" role="document">
				<div class="modal-content">
					<div class="modal-body">					
						<!-- Incoming Call -->
						<div class="call-box incoming-box">
							<div class="call-wrapper">
								<div class="call-inner">
									<div class="call-user">
										<img class="call-avatar" :src="url + '/'+JSON.parse(selected_user.photo)['s']" alt="User Image">
										<h4>{{ selected_user.fname + ' ' + selected_user.lname }}</h4>
										<span>{{ lang=='en'?'Calling ...':'جارٍ الاتصال ...'}}</span>
									</div>							
									<div class="call-items">
										<a href="javascript:void(0);" class="btn call-item call-end" data-dismiss="modal" aria-label="Close" id="vmodal-close-btn" @click="closeModal"><i class="material-icons">call_end</i></a>
										<a href="javascript:void(0);" class="btn call-item call-start" data-dismiss="modal" aria-label="Close" id="video-start-btn" @click="requestCall"><i class="material-icons" >videocam</i></a>
									</div>
								</div>
							</div>
						</div>
						<!-- /Incoming Call -->
						
					</div>
				</div>
			</div>
		</div>
		<!-- Video Call Modal -->  
    </div>
</template>


<script>
    import Twilio, { connect,  createLocalTracks, createLocalVideoTrack } from 'twilio-video'
    import axios from 'axios'
    import image from "./file-view.png";

    export default {
        name:'internal-chat-component',
        props:['user','selected_user','consuls','secrets','url','mission','internalroom','lang'],
        data() {
            return {
                messages: [],
                newMessage:'',
                users:[],
                activeUser: false,
                typingTimer: false,
                new_selected_user: '',
                unread:[],
                videocall:false,
                callrequest: false,
                videoConnected:false,
                videoZoomInOut:'table',
                zoom_flag:true,
                accessToken: '',
                roomId:'',
                roomN:'',
                activeRoom: null,
                full_style:{}
            }
        },
        created() {           
            this.consuls.forEach((con) => {
                this.unread.push({
                    key:con.user_id,
                    val:0
                });
            });
            this.secrets.forEach((sec) => {
                this.unread.push({
                    key:sec.user_id,
                    val:0
                });
            });            
                this.fetchMessages();
                Echo.join('internal'+this.mission)
                .here(user => {
                    this.users = user;
                })
                .joining(user => {
                    this.users.push(user);
                })      
                .leaving(user => {
                    this.users = this.users.filter(u => u.id != user.id);
                })                      
                .listen('InternalChatEvent',(event) => {
                    if (event.chat.from == this.selected_user.uid && event.chat.to == this.user.id)  {
                        if (event.chat.room_id != this.internalroom) this.internalroom = event.chat.room_id;            
                        this.messages.push(event.chat);    
                    }                
                    else {
                        const from_id = event.chat.from;                        
                        this.unread.forEach((un)=>{
                            if (un.key == event.chat.from) un.val++;
                        })                        
                    }
                })              
                .listenForWhisper('typing', user => {                    
                    if (user.id == this.selected_user.uid) this.activeUser = user;
                    if(this.typingTimer) { clearTimeout(this.typingTimer); }
                    this.typingTimer = setTimeout(() => { this.activeUser = false; }, 1000);
                });  
                Echo.channel('internalcall'+this.internalroom)
                    .listen('InternalCallEvent',(event)=>{
                         if (event.status == 1) {
                            this.videocall = false;
                            this.callrequest = false;
                            this.messages.push({
                                room_id: this.internalroom,
                                from: this.selected_user.uid,
                                to: this.user.id,
                                message:this.selected_user.fname + ' ' + this.selected_user.lname +' ended call',
                                date: new Date().toLocaleString(),
                            });
                         } else if (event.status == 2) {
                            this.videocall = false;
                            this.messages.push({
                                room_id: this.internalroom,
                                from: this.selected_user.uid,
                                to: this.user.id,
                                message:this.selected_user.fname + ' ' + this.selected_user.lname +' declined call',
                                date: new Date().toLocaleString(),
                            });
                         }
                         else {
                            this.callrequest = true;
                         }
                    })

        },
        methods: {
            fetchMessages() {
                axios.get('/internal-chat/messages?u1='+this.user.id+'&u2='+this.selected_user.uid).then(response => {
                    this.messages = response.data;
                })
            },
            sendMessage() {
                let formData = new FormData();  
                formData.append('room_id',this.internalroom);
                formData.append('from',this.user.id);
                formData.append('to',this.selected_user.uid);
                formData.append('message',this.newMessage);

                this.messages.push({
                    room_id: this.internalroom,
                    from: this.user.id,
                    to: this.selected_user.uid,
                    message:this.newMessage,
                    created_at: new Date().toLocaleString(),
                });

                axios.post('/internal-chat/messages', formData ,
                    {
                        headers: {
                            'Content-Type': 'multipart/form-data'
                        }
                    }
                );
                this.newMessage = '';
            },
            sendTypingEvent() {
                Echo.join('internal'+this.mission)
                    .whisper('typing', this.user);
                //console.log(this.user.role + ' is typing now')
            },
            setMessagePosition(from, to) {
                if (from == this.user.id && to==this.selected_user.uid) return 'sent';
                if (to == this.user.id && from==this.selected_user.uid) return 'received';
            },
            requestCall() {
                let formData = new FormData();  
                formData.append('introom', this.internalroom);
                axios.post('/api/internal_call_request', formData);
                this.videocall = true;
                this.roomN = 'introom-'+this.internalroom;
                this.connectRoom(this.roomN);
                this.closeModal();  
            },
            closeModal() {

            },
            acceptCall() {
                //console.log('Video room loading...')
                this.videocall = true;
                this.callrequest = false;
                this.roomN = 'introom-'+this.internalroom;
                this.connectRoom(this.roomN);          
            },
            declineCall() {
                this.callrequest = false;
                //send  decline message
                let formData = new FormData();  
                formData.append('introom', this.internalroom);
                axios.post('/api/decline_internal_request',formData).then((data)=>{
                   // console.log('decline video call');                
                      this.messages.push({
                        room_id: this.internalroom,
                        from: this.user.id,
                        to: this.selected_user.uid,
                        message:this.user.fname + ' ' + this.user.lname +' declined call',
                        date: new Date().toLocaleString(),
                       });
                });
            },
            endCall() {
                createLocalVideoTrack().then(track => {
                    track.stop();
                });
                //end call event
                //Leave Room
                let formData = new FormData();  
                formData.append('rId', this.roomId);
                formData.append('introom', this.internalroom);
                axios.post('/api/end_internal_request',formData).then((data)=>{
                    //console.log('end video call');
                    this.videocall = false;
                    this.callrequest = false;

                    this.messages.push({
                        room_id: this.internalroom,
                        from: this.user.id,
                        to: this.selected_user.uid,
                        message:this.user.fname + ' ' + this.user.lname +' ended call',
                        date: new Date().toLocaleString(),
                    });
                });
            },
            async getAccessToken() {
                return await axios.get('/api/internal_token?introom='+ this.internalroom);
            },   
            connectRoom(roomName) {
                const VueThis = this;
                 this.getAccessToken().then( (data) => {
                    var token = data.data;
                    // remove any remote track when joining a new room
                    Twilio.connect(token, { name: roomName }).then(room => {
                        this.activeRoom = room;
                        //console.log('Connected to Room "%s"', room.name);
                        this.roomId = room.sid;
                        room.participants.forEach((participant)=>{                            
                            //console.log('Participant "%s" connected', participant.identity);
                            const div = document.getElementById('user-video');
                            participant.on('trackSubscribed', track => {
                                div.appendChild(track.attach());
                            });
                            participant.on('trackUnsubscribed', (track)=>{
                                track.detach().forEach(element => element.remove());
                            });
                            participant.tracks.forEach(publication => {
                                if (publication.isSubscribed) {
                                    div.appendChild(publication.track.attach());
                                }
                            });
                            this.videoConnected = true;
                        });
                        room.on('participantConnected', (participant)=>{
                            //console.log('Participant "%s" connected', participant.identity);                          
                            const div = document.getElementById('user-video');
                            participant.on('trackSubscribed', track => {
                                div.appendChild(track.attach());
                            });
                            participant.on('trackUnsubscribed', (track)=>{
                                track.detach().forEach(element => element.remove());
                            });
                            participant.tracks.forEach(publication => {
                                if (publication.isSubscribed) {
                                    div.appendChild(publication.track.attach());
                                }
                            });
                            this.videoConnected = true;
                        });

                        room.on('participantDisconnected', (participant)=>{
                            //console.log('Participant "%s" disconnected', participant.identity);
                            //document.getElementById(participant.sid).remove();                                               
                        });
                        room.once('disconnected', error => room.participants.forEach( (participant) => {
                                //console.log('Participant "%s" disconnected', participant.identity);                          
                            })
                        );
                        //Show My video 
                        const videoChatWindow = document.getElementById('my-video');
                        createLocalVideoTrack().then(track => {
                            videoChatWindow.appendChild(track.attach());
                        });
                    });

                 });
            },
            zoomIn () {
                this.zoom_flag = true;
                this.videoZoomInOut = 'table';
            },
            zoomOut () {
                this.zoom_flag = false;
                this.videoZoomInOut = 'none';
                this.full_style = {};
            },
            fullScreen() {
                this.full_style = {
                    height: '100vh',
                    position: 'fixed',
                    top: 0,
                    right: 0,
                };
            }
        }
    }
</script>