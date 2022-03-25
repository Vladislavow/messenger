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
    created() {},
};
</script>

<style scoped></style>
