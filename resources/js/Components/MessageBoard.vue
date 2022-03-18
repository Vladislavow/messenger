<template>
    <div class="board">
        <message-list
            :loading="this.loading"
            :messages="this.messages"
            :userId="userId"
            :page="this.page"
            :totalPages="this.totalPages"
            @loaded="stopLoading"
            @getMessages="getMessages"
        />
        <message-creator @send="send" />
    </div>
</template>

<script>
import MessageCreator from "./MessageCreator.vue";
import MessageList from "./MessageList.vue";
export default {
    components: { MessageList, MessageCreator },
    data: () => {
        return {
            messages: [],
            loading: false,
            page: 1,
            totalPages: 1,
        };
    },
    mounted() {
        Echo.private(`messages.${this.userId}`).listen("NewMessage", (e) => {
            this.handleIncoming(e.message);
        });
    },
    watch: {
        chat: function (val) {
            this.getMessages();
        },
    },
    methods: {
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
            if (this.chat == message.sender) {
                this.messages.push(message);
                this.setRead();
            } else {
                this.$store.dispatch("setUnread", {
                    sender: message.sender,
                    count: 1,
                });
            }
        },
        send(text) {
            axios
                .post("/api/chats/messages", {
                    content: text,
                    sender: this.userId,
                    recipient: this.chat,
                })
                .then((resp) => {
                    this.messages.push(resp.data);
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
            return parseInt(localStorage.getItem("useerid"));
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
