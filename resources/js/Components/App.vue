<template>
    <div>
        <navbar />
        <chat-list />
        <message-board />
    </div>
</template>

<script>
import MessageBoard from "./MessageBoard.vue";
import ChatList from "./ChatList.vue";
import Navbar from "./Navbar.vue";
export default {
    components: { MessageBoard, ChatList, Navbar },
    data: () => {
        return {};
    },
    methods: {},
    created() {
        if (Object.keys(this.$store.getters.user).length == 0) {
            this.$store.dispatch("getUser");
        } else {
        }
    },
};
</script>

<style scoped></style>
