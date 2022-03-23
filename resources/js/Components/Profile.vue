<template>
    <div class="profile">
        <div class="close">
            <v-icon @click.prevent="closeProfile" color="white" large>
                mdi-close
            </v-icon>
        </div>
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
            <div>
                <v-icon dark>mdi-email-outline</v-icon>{{ " " + chat.email }}
            </div>
            <div>
                <v-icon dark>mdi-phone-outline</v-icon>{{ " " + chat.phone }}
            </div>
            <div v-if="chat.bio">
                <v-icon dark>mdi-cake-variant-outline</v-icon
                >{{ " " + chat.birthdate }}
            </div>
            <div v-if="chat.bio">
                <v-icon dark>mdi-information-outline</v-icon
                >{{ " " + chat.bio }}
            </div>
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
        },
    },
    created() {
        this.getChat();
    },
    methods: {
        getChat() {
            axios.get("/api/chat/" + this.chatId).then((resp) => {
                this.chat = resp.data;
            });
        },
        closeProfile() {
            this.$store.dispatch("setSelectedProfile", null);
        },
    },
    computed: {
        chatId: function () {
            return this.$store.getters.selectedProfile;
        },
    },
};
</script>

<style scoped>
.img-container {
    margin-top: 10px;
}
.profile {
    position: fixed;
    width: 25%;
    height: 100%;
    right: 0;
    background: rgb(33, 33, 33);
    border-left: 1px solid black;
}
.image {
    margin-left: 30%;
    margin-bottom: 10px;
    min-height: 50px;
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
.information * {
    padding: 5px 7px;
    border-radius: 10px;
    margin: 2px;
}
.information *:hover {
    background: rgb(77, 73, 73);
}
.close {
    position: absolute;
    left: 10px;
    top: 10px;
}
</style>
