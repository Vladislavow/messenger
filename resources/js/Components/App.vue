<template>
    <div>
        <navbar />
        <chat-list />
        <message-board />
        <image-viewer v-if="this.$store.getters.selectedImage" />
    </div>
</template>

<script>
import MessageBoard from "./MessageBoard.vue";
import ChatList from "./ChatList.vue";
import Navbar from "./Navbar.vue";
import Echo from "laravel-echo";
import ImageViewer from './ImageViewer.vue';
export default {
    components: { MessageBoard, ChatList, Navbar, ImageViewer },
    data: () => {
        return {
        };
    },
    methods: {},
    mounted() {
        this.changeOnlineStatus(true);
        window.addEventListener(
            "beforeunload",
            function (e) {
                e.preventDefault();
                axios
                    .post("/api/user/online", { status: false })
                    .then((resp) => {});
            },
            false
        );
        Notification.requestPermission();
        this.connectToPusher();
    },
    created() {},
    methods: {
        changeOnlineStatus(status) {
            axios
                .post("/api/user/online", { status: status })
                .then((resp) => {});
        },
        connectToPusher() {
            window.Echo = new Echo({
                broadcaster: "pusher",
                key: process.env.MIX_PUSHER_APP_KEY,
                cluster: process.env.MIX_PUSHER_APP_CLUSTER,
                encrypted: true,
                authEndpoint: `/api/broadcasting/auth`,
                auth: {
                    headers: {
                        Authorization: `Bearer ${localStorage.getItem(
                            "token"
                        )}`,
                    },
                },
            });
        },
    },
};
</script>

<style scoped></style>
