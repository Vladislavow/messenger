<template>
    <div class="board">
        <message-list :messages="this.messages" :userId="userId" />
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
                .get(`/api/chat/${this.chat}/messages`)
                .then((resp) => [(this.messages = resp.data)]);
        },
        handleIncoming(message) {
            if (this.chat == message.sender) {
                this.messages.push(message);
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
    },
    computed: {
        chat: function () {
            return this.$store.getters.selectedChat;
        },
        userId: function () {
            return localStorage.getItem("useerid");
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
