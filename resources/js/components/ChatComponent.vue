<template>
   <div class="row">
        <div class="col-md-12">
        <div v-if="user.role=='client'" class="card success-card m-auto ">
            <div class="card-body">
                <div class="d-flex" style="justify-content:space-around">
                <p class="text-info"><i class="fa fa-users "></i> {{totaluser}} {{ lang=='en'?'Clients':'عميل'}} </p>
                <p class="text-success"><i class="fa fa-users "></i> {{doneuser}} {{ lang=='en'?'Finished':'تم الانتهاء من' }} </p>
                <p class="text-muted"><i class="fa fa-users "></i> {{pendinguser}} {{ lang=='en'?'Pending':'قيد الانتظار' }}</p>
                </div>
            </div>
        </div> 
        </div>
        <div class="col-md-12">
            <div class="chat-header">
                <div class="media">
                    <div class="media-img-wrap">
                        <div class="avatar avatar-online">
                            <img :src="avatarImage" alt="User Image" class="avatar-img rounded-circle">
                        </div>
                    </div>
                    <div class="media-body">
                        <div class="user-name">{{recepiant.fname + ' ' + recepiant.lname}}</div>
                        <div v-if="connected" class="user-status">{{ lang=='en'? 'Connected':'متصل' }}</div>
                        <div v-if="!connected" class="user-status">{{  lang=='en'?'Waiting..':'منتظر' }}</div>
                    </div>
                </div>
                <div  v-if="user.role=='consul'" class="chat-options"  >
                    <a href="javascript:void(0)" data-toggle="modal" data-target="#close_meeting">
                        <i class="material-icons">close</i>
                    </a>                
                    <a href="javascript:void(0)" data-toggle="modal" data-target="#video_call">
                        <i class="material-icons">videocam</i>
                    </a>
                    <a v-if="!zoom_flag" href="javascript:void(0)" @click="zoomIn">
                        <i class="material-icons">zoom_in</i>
                    </a>
                </div>
                <div  v-if="user.role=='client'" class="chat-options">                  
                    <a v-if="!zoom_flag" href="javascript:void(0)" @click="zoomIn">
                        <i class="material-icons">zoom_in</i>
                    </a>
                </div>
            </div>
            <div class="chat-body">
                <!-- Call Wrapper -->
                <div v-if="videocall" class="call-wrapper video-call-wrapper" :style="full_style">
                    <div class="call-main-row">
                        <div class="call-main-wrapper" >
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
                                            <div v-if="user.role=='consul'" class="end-call">
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
                <!-- /Call Wrapper --> 
                <div class="chat-scroll" v-chat-scroll>
                <ul class="list-unstyled">
                    <li class="media" 
                        v-for="(message,index) in messages" :key="index"
                        :class="setMessagePosition(message.sender)">

                        <div v-if="showAvatar(message.sender)" class="avatar">
                            <img :src="avatarImage" alt="User Image" class="avatar-img rounded-circle">
                        </div>  

                        <div class="media-body">
                            <div v-if="message.message!=''" class="msg-box">
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

                            <div v-if="message.hasfile" class="msg-box">
                                <div>
                                    <div class="chat-msg-attachments">
                                        <div class="chat-attachment">
                                            <img :src="fileicon" alt="Attachment">
                                            <div class="chat-attach-caption"> {{ message.file}} </div>
                                            <a :href="message.path" class="chat-attach-download" download>
                                                <i class="fas fa-download"></i>
                                            </a>
                                        </div>
                                    </div>

                                    <ul class="chat-msg-info">
                                        <li>
                                            <div class="chat-time">
                                                 <Timeago :datetime="message.created_at" :auto-update="10" ></Timeago>
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
            <div v-if="!finished " class="chat-footer">
                <div class="input-group">   
                    <div class="input-group-prepend">
                        <div class="btn-file btn">
                            <i class="fa fa-paperclip"></i>
                            <input type="file" @change="previewFiles">
                        </div>
                    </div>                        
                    <input 
                        type="text" class="input-msg-send form-control"
                        :placeholder="lang=='en'?'Type something':'اطبع شيئا'"
                        v-model = "newMessage"
                        @keyup.enter="sendMessage"
                        @keydown="sendTypingEvent"
                    >
                    <div class="input-group-append">
                        <button type="button"
                                class="btn msg-send-btn"
                                @click="sendMessage">
                                <i class="fab fa-telegram-plane"></i>
                        </button>
                    </div>
                </div>
                <span class="text-muted" v-if="activeUser" > {{ lang =='en'?'typing...':'الكتابة ..'}}</span>
            </div>
             <div v-if="finished && user.role=='client' " class="chat-footer">
                <div class="form-group text-center">{{ lang=='en'?'Finished meeting successfully.':'انتهى الاجتماع بنجاح.'}}</div>
            </div>
            <div v-if="fileupload" class="chat-footer">
                <div class="form-group text-center">{{ selected_filename }} {{ lang=='en'?'selected.':'المحدد'}}</div>
            </div>
            <div v-if="callrequest" class="chat-footer">
                <div class="form-group text-center">
                    <h4>{{ lang=='en'? 'Video calling from':'مكالمات الفيديو من'}}  {{recepiant.fname + ' ' + recepiant.lname}} </h4>
                    <a href="javascript:void(0)" class="btn btn-danger btn-sm ml-2 mr-2" @click="declineCall">{{lang=='en'?'Decline':'انخفاض'}}</a>
                    <a href="javascript:void(0)" class="btn btn-success btn-sm ml-2 mr-2" @click="acceptCall">{{ lang=='en'?'Accept':'قبول'}}</a>
                </div>
            </div>
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
										<img class="call-avatar" :src="avatarImage" alt="User Image">
										<h4>{{ recepiant.fname + ' ' + recepiant.lname }}</h4>
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
        props:['user','avatarImage','bkId','recepiant','total','pending','done','booking','lang'],
        data() {
            return {
                messages: [],
                newMessage: '',
                users:[],
                activeUser: false,
                typingTimer: false,
                connected: false,
                finished:false,
                fileicon: image,
                uploadfile:'',
                selected_filename:'',
                fileupload:false,
                callrequest:false,
                accessToken: '',
                zoom_flag: true,
                videocall: false,
                videoConnected:false,
                videoZoomInOut:'table',
                roomId:'',
                roomN:'',
                activeRoom: null,
                totaluser:0,
                doneuser:0,
                pendinguser:0,
                full_style:{}
            }
        },
        created() {  
            this.fetchMessages();

            Echo.join('room'+this.bkId)
                .here(user => {
                    this.users = user;
                })
                .joining(user => {
                    //console.log('appliciant connected');
                    this.connected = true;
                    this.users.push(user);
                })
                .leaving(user => {
                    //console.log('appliciant left room');
                    this.connected = false;
                    this.users = this.users.filter(u => u.id != user.id);
                })
                .listen('ChatEvent',(event) => {
                    this.connected = true;
                    this.finished = event.status;
                    this.messages.push(event.chat);                    
                })
                .listenForWhisper('typing', user => {
                    this.connected = true;
                    this.activeUser = user;
                    if(this.typingTimer) {
                        clearTimeout(this.typingTimer);
                    }
                    this.typingTimer = setTimeout(() => {
                        this.activeUser = false;
                    }, 1000);
                });

            Echo.channel('call'+this.bkId)
                .listen('CallEvent',(event)=>{
                    //console.log('Call Status='+ event.status);
                    if (event.status == 1) {
                        //console.log('end video call');
                        this.videocall = false;
                        this.callrequest = false;

                        this.messages.push({
                            user: this.user,
                            sender: 'consul',
                            message: 'Ended Video Call',
                            created_at: new Date().toLocaleString(),
                            file: '',
                            path: '',
                            hasfile: false
                        });

                    }else if (event.status == 2) {
                        //console.log('decline  receive');                    
                        this.videocall = false;

                        this.messages.push({
                            user: this.user,
                            sender: 'client',
                            message: 'Declined Video Call',
                            created_at: new Date().toLocaleString(),
                            file: '',
                            path: '',
                            hasfile: false
                        });

                    } else {
                        this.callrequest = true;
                    }
            });

            if (this.user.role == 'client') {
                Echo.channel('group'+this.booking.schedule_date+this.booking.start_time.replace(':','')+this.booking.end_time.replace(':',''))
                    .listen('GroupScheduleEvent',(event)=>{
                        if (event.status == 0) {
                            // none
                        } else if (event.status == 1) {
                            // one finished
                            this.doneuser++;
                            this.pendinguser--;
                        } else {
                            // your turn
                            //this.connected = true;                            
                            //var t = this.lang=='en'?'You can chat with ':' دورك! يمكنك الدردشة مع';
                            //if (this.user.role == 'client' && !this.finished) Vue.swal(t + this.recepiant.fname+ ' ' + this.recepiant.lname);
                        }
                    })
            }
            this.totaluser = this.total;
            this.pendinguser = this.pending;
            this.doneuser = this.done;
        },
        methods: {
            previewFiles(event) {
                this.uploadfile = event.target.files[0];
                this.fileupload = true;
                this.selected_filename = event.target.files[0].name;
            },
            fetchMessages() {
                axios.get('/messages/'+this.bkId).then(response => {
                    this.messages = response.data;
                })
            },
            sendMessage() {

                let formData = new FormData();  
                if (this.fileupload) {                             
                    formData.append('file', this.uploadfile);
                } 
                formData.append('bid',this.bkId);
                formData.append('uid',this.user.id);
                formData.append('message',this.newMessage);

                this.messages.push({

                    user: this.user,
                    sender: this.user.role,
                    message: this.newMessage,
                    created_at: new Date().toLocaleString(),
                    file: this.selected_filename,
                    path: '',
                    hasfile: this.fileupload?true:false
                    
                });

                axios.post('/messages', formData ,
                    {
                        headers: {
                            'Content-Type': 'multipart/form-data'
                        }
                    }
                );
                this.newMessage = '';
                this.fileupload = false;
            },
            sendTypingEvent() {
                Echo.join('room'+this.bkId)
                    .whisper('typing', this.user);
                //console.log(this.user.role + ' is typing now')
            },
            setMessagePosition(sender) {
                // return 'sent' or 'received'
                if (sender == 'consul') {
                    if (this.user.role == 'client') {
                        return 'received';
                    } else {
                        return 'sent';
                    }
                }
                if (sender == 'client') {
                    if (this.user.role == 'client') {
                        return 'sent';
                    } else {
                        return 'received';   
                    }
                }                
            },
            showAvatar(sender) {
                if (sender == 'consul') {
                    if (this.user.role == 'client') {
                        return true;
                    } else {
                        return false;
                    }
                }
                if (sender == 'client') {
                    if (this.user.role == 'client') {
                        return false;
                    } else {
                        return true;
                    }
                }              
            },
            currentDateTime() {
                const current = new Date();
                const date = current.getFullYear()+'-'+(current.getMonth()+1)+'-'+current.getDate();
                const time = current.getHours() + ":" + current.getMinutes() + ":" + current.getSeconds();
                const dateTime = date +' '+ time;
                
                return dateTime;
            },
            leaveMeeting() {
                //console.log('leave meeting room');
                axios.post('/leave', {bid: this.bkId, uid:this.user.id});
            },
            requestCall() {
                let formData = new FormData();  
                formData.append('room', this.bkId);
                axios.post('/api/call_request', formData);
                this.videocall = true;
                this.roomN = 'vidroom'+this.bkId;
                this.connectRoom('vidroom'+this.bkId);
                this.closeModal();                
            },
            closeModal() {},
            declineCall() {
                this.callrequest = false;
                //send  decline message
                let formData = new FormData();  
                formData.append('bkId', this.bkId);
                axios.post('/api/decline_request',formData).then((data)=>{
                   // console.log('decline video call');                
                    this.messages.push({
                            user: this.user,
                            sender: 'client',
                            message: 'Declined Video Call',
                            created_at: new Date().toLocaleString(),
                            file: '',
                            path: '',
                            hasfile: false
                    });
                });


            },
            acceptCall() {
                //console.log('Video room loading...')
                this.videocall = true;
                this.callrequest = false;
                this.roomN = 'vidroom'+this.bkId;
                this.connectRoom('vidroom'+this.bkId);
            },
            endCall() { 
                createLocalVideoTrack().then(track => {
                    track.stop();
                });
                //end call event
                //Leave Room
                let formData = new FormData();  
                formData.append('rId', this.roomId);
                formData.append('bkId', this.bkId);
                axios.post('/api/end_request',formData).then((data)=>{
                    //console.log('end video call');
                    this.videocall = false;
                    this.callrequest = false;

                    this.messages.push({
                        user: this.user,
                        sender: this.user.role,
                        message: 'End Video Call',
                        created_at: new Date().toLocaleString(),
                        file: '',
                        path: '',
                        hasfile: false
                    });
                });                        ;
            },
            async getAccessToken() {
                return await axios.get('/api/access_token?bk='+ this.bkId);
            },   
            connectRoom(roomName) {
                const VueThis = this;
                this.getAccessToken().then( (data) => {
                    //console.log(data);
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
                })
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