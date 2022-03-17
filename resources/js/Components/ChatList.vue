<template>
    <div class="chats">
        <div v-for="(chat, index) in this.chats" :key="index">
            <chat-item :chat="chat" />
        </div>
    </div>
</template>

<script>
import ChatItem from "./ChatItem.vue";
export default {
    components: { ChatItem },
    data: () => {
        return {
            chats: [],
        };
    },
    methods: {
        getChatList() {
            axios.get("/api/chats").then((resp) => {
                this.chats = resp.data;
            });
        },
    },
    created() {
        this.getChatList();
    },
};
</script>

<style scoped>
.chats {
    position: fixed;
    width: 25%;
    background-color: rgb(65, 40, 40);
    height: 92%;
    bottom: 0;
    left: 0;
    overflow: scroll;
    overflow-x: hidden;
}
</style>
