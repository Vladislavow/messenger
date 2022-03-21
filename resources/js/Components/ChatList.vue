<template>
    <div class="chats">
        <div v-for="(chat, index) in chats" :key="index">
            <chat-item :chat="chat" />
        </div>
    </div>
</template>

<script>
import ChatItem from "./ChatItem.vue";
export default {
    components: { ChatItem },
    data: () => {
        return {};
    },
    methods: {
        getChatList() {
            this.$store.dispatch("getChats");
        },
    },
    created() {
        this.getChatList();
    },
    computed: {
        selected: function () {
            return this.$store.getters.selectedChat;
        },
        chats: function () {
            return this.$store.getters.chats;
        },
    },
};
</script>

<style scoped>
.chats {
    position: fixed;
    width: 25%;
    background-color: rgb(33, 33, 33);
    top: 58px;

    bottom: 0;
    left: 0;
    overflow: scroll;
    overflow-x: hidden;
    border-right: 1px solid black;
}
.chats::-webkit-scrollbar {
    width: 5px;
    border-radius: 100px;
}
.chats::-webkit-scrollbar-thumb {
    background-color: white;
    display: none;
    border-radius: 5px;
}
.chats::-webkit-scrollbar-thumb:hover {
    display: initial;
}

@media (max-width: 700px) {
    .chats {
        width: 80px;
    }
}
</style>
