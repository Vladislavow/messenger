<template>
    <div class="board">
        <message-nav :class="this.selectedProfile ? '' : 'closedProfile'" />
        <message-list
            :class="this.selectedProfile ? '' : 'ms'"
            v-if="chat"
            :loading="this.loading"
            :messages="this.messages"
            :userId="userId"
            :page="this.page"
            :totalPages="this.totalPages"
            @loaded="stopLoading"
            @getMessages="getMessages"
            @deleteMessage="deleteMessage"
        />
        <message-creator
            :class="this.selectedProfile ? '' : 'ms'"
            v-if="chat"
            @send="send"
            :loading="this.loading"
        />
        <profile v-if="selectedProfile" />
        <v-progress-circular
            v-if="loading"
            :class="{ loadwp: this.selectedProfile, load: true }"
            color="white"
            size="50"
            class="load"
            indeterminate
        >
        </v-progress-circular>
    </div>
</template>

<script>
import MessageNav from "./MessageNav.vue";
import MessageCreator from "./MessageCreator.vue";
import MessageList from "./MessageList.vue";
import Profile from "./Profile.vue";
export default {
    components: { MessageList, MessageCreator, Profile, MessageNav },
    data: () => {
        return {
            messages: [],
            page: 1,
            totalPages: 1,
            loading: false,
            cancelToken: null,
            source: null,
        };
    },
    mounted() {
        Echo.private(`messages.${this.userId}`).listen("NewMessage", (e) => {
            this.handleIncoming(e.message);
        });
        Echo.private(`messages.${this.userId}`).listen("HasRead", (e) => {
            this.marAsRead(e.chat);
        });
        Echo.private(`messages.${this.userId}`).listen("ChatUpdated", (e) => {
            this.$store.dispatch("updateChat", e.chat);
        });
    },
    watch: {
        chat: function (val) {
            this.getMessages();
            axios.post(`/api/chat/${val}/markasread`);
        },
    },
    methods: {
        marAsRead(chat) {
            console.log("awd");
            if (this.chat == chat) {
                this.setRead();
            }
        },
        deleteMessage(message) {
            axios.delete("/api/message/" + message.id).then((response) => {
                let id = this.messages.indexOf(
                    this.messages.find((messagef) => messagef.id == message.id)
                );
                this.messages.splice(id, 1);

                let lastmessage = this.messages[this.messages.length - 1];
                this.$store.dispatch("setLastMessage", {
                    sender: this.chat,
                    message: lastmessage,
                });
            });
        },
        getMessages() {
            this.loading = true;
            if (this.source != null) {
                this.source.cancel();
            }
            this.CancelToken = axios.CancelToken;
            this.source = this.CancelToken.source();
            axios
                .get(`/api/chat/${this.chat}/messages?page=${this.page}`, {
                    cancelToken: this.source.token,
                })
                .then((resp) => {
                    if (this.page == 1) {
                        this.messages = [];
                    }
                    this.messages = resp.data.data
                        .reverse()
                        .concat(this.messages);
                    this.totalPages = resp.data.last_page;
                    this.page = this.page + 1;
                    this.loading = false;
                })
                .catch((err) => {
                    console.log(err);
                });
        },
        handleIncoming(message) {
            if (this.chat == message.sender) {
                this.messages.push(message);
                this.setRead();
                console.log("fckk");
                axios.post(`/api/chat/${message.sender}/markasread`);
            } else {
                this.$toast.info("New message!");
                this.$store.dispatch("checkForChatExists", message);
            }
            this.$store.dispatch("setLastMessage", {
                sender: message.sender,
                message: message,
            });
        },
        send(text) {
            this.loading = true;
            axios
                .post("/api/chats/messages", {
                    content: text,
                    sender: this.userId,
                    recipient: this.chat,
                })
                .then((resp) => {
                    this.messages.push(resp.data);
                    this.loading = false;
                    this.$store.dispatch("setLastMessage", {
                        sender: resp.data.recipient,
                        message: resp.data,
                    });
                });
        },
        stopLoading() {
            this.loading = false;
        },
        setRead() {
            this.messages.forEach((message) => {
                if (message.sender == this.userId) {
                    message.read = true;
                }
            });
        },
    },
    computed: {
        chat: function () {
            this.messages = [];
            this.page = 1;
            return this.$store.getters.selectedChat;
        },
        userId: function () {
            return parseInt(localStorage.getItem("userid"));
        },
        selectedProfile: function () {
            return this.$store.getters.selectedProfile;
        },
    },
};
</script>

<style scoped>
.board {
    height: 100%;
    width: 75%;
    background: rgb(33, 33, 33);
    position: fixed;
    top: 0;
    right: 0;
}
.ms {
    margin-left: 12.5%;
}
.closedProfile {
    width: 100%;
}
.load {
    position: absolute;
    top: 45%;
    left: 50%;
}
.loadwp {
    left: 35%;
}
@media (max-width: 700px) {
    .board {
        left: 14%;
        right: 25%;
    }
}
</style>
