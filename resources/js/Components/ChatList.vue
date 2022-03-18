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
    watch: {
        selected: function (val) {},
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
    height: 92%;
    bottom: 0;
    left: 0;
    overflow: scroll;
    overflow-x: hidden;
}
.chats::-webkit-scrollbar {
    width: 3px;
    border-radius: 100px;
}
.chats::-webkit-scrollbar-thumb {
    background-color: white;
}
</style>
