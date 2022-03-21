<template>
    <div class="profile">
        <div class="img-container">
            <v-img
                class="image"
                :src="this.chat.avatar"
                max-height="40%"
                max-width="40%"
            >
            </v-img>
        </div>
        <v-divider dark />
        <div class="name">
            {{ chat.firstname + " " + chat.lastname }}
        </div>
        <div class="information">
            <div><v-icon dark>mdi-at</v-icon>{{ " " + chat.nickname }}</div>
            <div><v-icon dark>mdi-email</v-icon>{{ " " + chat.email }}</div>
            <div><v-icon dark>mdi-phone</v-icon>{{ " " + chat.phone }}</div>
            <div>
                <v-icon dark>mdi-cake-variant</v-icon>{{ " " + chat.birthdate }}
            </div>
            <div><v-icon dark>mdi-information</v-icon>{{ " " + chat.bio }}</div>
        </div>
    </div>
</template>

<script>
export default {
    data: () => {
        return {
            chat: {},
        };
    },
    watch: {
        chatId: function () {
            this.getChat();
            console.log("hi");
        },
    },
    created() {},
    methods: {
        getChat() {
            axios.get("/api/chat/" + this.chatId).then((resp) => {
                this.chat = resp.data;
            });
        },
    },
    computed: {
        chatId: function () {
            return this.$store.getters.selectedChat;
        },
    },
};
</script>

<style scoped>
.profile {
    position: fixed;
    width: 25%;
    height: 100%;
    right: 0;
    background: rgb(33, 33, 33);
}
.image {
    margin-left: 30%;
    margin-bottom: 10px;
}
.name {
    color: white;
    font-size: 30px;
    word-break: break-all;
}
.information {
    color: white;
    font-size: 22px;
}
</style>
