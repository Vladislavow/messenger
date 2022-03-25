<template>
    <div class="chats">
        <div v-for="(chat, index) in chats" :key="index">
            <chat-item :chat="chat" />
        </div>
        <div v-if="this.chats.length == 0" class="empty">
            {{
                this.$store.getters.search
                    ? "No results..."
                    : "No chats started yet"
            }}
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
            if (this.$store.getters.search) {
                return this.$store.getters.searchedChats;
            }
            var chats = this.$store.getters.chats;
            return chats.sort(sortByDate);
        },
        serchedChats: function () {
            return this.$store.getters.searchedChats;
        },
    },
};
var sortByDate = function (d1, d2) {
    return !d1.last_message || !d2.last_message
        ? 1
        : new Date(d1.last_message.created_at) >
          new Date(d2.last_message.created_at)
        ? -1
        : 1;
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
        min-width: 14%;
        max-width: 14%;
    }
}
.empty {
    color: white;
    text-align: center;
    font-size: 24px;
    margin-top: 10px;
}
</style>
