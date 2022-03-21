<template>
    <div class="board">
        <message-list
            v-if="chat"
            :loading="this.loading"
            :messages="this.messages"
            :userId="userId"
            :page="this.page"
            :totalPages="this.totalPages"
            @loaded="stopLoading"
            @getMessages="getMessages"
        />
        <message-creator v-if="chat" @send="send" :loading="this.loading" />
        <profile />
    </div>
</template>

<script>
import MessageCreator from "./MessageCreator.vue";
import MessageList from "./MessageList.vue";
import Profile from "./Profile.vue";
export default {
    components: { MessageList, MessageCreator, Profile },
    data: () => {
        return {
            messages: [],
            page: 1,
            totalPages: 1,
            loading: false,
        };
    },
    mounted() {
        Echo.private(`messages.${this.userId}`).listen("NewMessage", (e) => {
            this.handleIncoming(e.message);
        });
        Echo.private(`messages.${this.userId}`).listen("HasRead", (e) => {
            this.marAsRead(e.chat);
        });
    },
    watch: {
        chat: function (val) {
            axios.post(`/api/chat/${val}/markasread`);
            this.getMessages();
        },
    },
    methods: {
        marAsRead(chat) {
            console.log("awd");
            if (this.chat == chat) {
                this.setRead();
            }
        },
        getMessages() {
            axios
                .get(`/api/chat/${this.chat}/messages?page=${this.page}`)
                .then((resp) => {
                    this.messages = resp.data.data
                        .reverse()
                        .concat(this.messages);
                    this.totalPages = resp.data.last_page;
                    this.page = this.page + 1;
                });
        },
        handleIncoming(message) {
            console.log(message.sender + " " + this.chat);
            if (this.chat == message.sender) {
                this.messages.push(message);
                this.setRead();
                console.log("fckk");
                axios.post(`/api/chat/${message.sender}/markasread`);
            } else {
                this.$store.dispatch("setUnread", {
                    sender: message.sender,
                    count: 1,
                });
            }
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
            this.loading = true;
            this.page = 1;
            return this.$store.getters.selectedChat;
        },
        userId: function () {
            return parseInt(localStorage.getItem("userid"));
        },
    },
};
</script>

<style scoped>
.board {
    height: 100%;
    width: 75%;
    background: black;
    position: fixed;
    top: 0;
    right: 0;
}
</style>
