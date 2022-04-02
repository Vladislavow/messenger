<template>
    <div>
        <div :class="{ selected: this.selected, chat }" @click="selectChat">
            <v-img class="avatar" :src="chat.avatar">
                <template v-slot:placeholder>
                    <v-row
                        class="fill-height ma-0"
                        align="center"
                        justify="center"
                    >
                        <v-progress-circular
                            indeterminate
                            color="grey lighten-5"
                        ></v-progress-circular>
                    </v-row>
                </template>
            </v-img>
            <div class="content">
                <div class="title">{{ getTitle() }}</div>
                <div v-if="chat.last_message" class="lastMessage">
                    {{ getLastMessage() }}
                    <v-icon
                        v-if="chat.last_message.attachments.length > 0"
                        color="rgb(204, 197, 197)"
                        >mdi-attachment</v-icon
                    >
                </div>
            </div>
            <div v-if="chat.last_message && myMessage()" class="my_unread">
                <v-icon small color="white">{{
                    chat.last_message.read ? "mdi-check-all" : "mdi-check"
                }}</v-icon>
            </div>
            <div
                :class="{
                    unread: chat.unread > 0,
                    'none-unread': chat.unread <= 0,
                }"
            >
                {{ chat.unread > 99 ? "99+" : chat.unread }}
            </div>
            <div class="online" v-if="chat.status == true"></div>
        </div>
    </div>
</template>

<script>
export default {
    props: ["chat"],
    methods: {
        selectChat() {
            this.$store.dispatch("selectChat", this.chat.id);
        },
        getLastMessage() {
            return this.chat.last_message.content.length > 35
                ? this.chat.last_message.content.substr(0, 35) + "..."
                : this.chat.last_message.content;
        },
        getTitle() {
            let name = this.chat.firstname + " " + this.chat.lastname;
            return name.length > 17 ? name.substr(0, 17) + "..." : name;
        },
        myMessage() {
            return (
                this.chat.last_message.sender == localStorage.getItem("userid")
            );
        },
    },
    computed: {
        selected: function () {
            return this.$store.getters.selectedChat == this.chat.id;
        },
    },
};
</script>

<style scoped>
.chat {
    min-height: 60px;
    /* border-radius: 10px; */
    margin: 10px;
    width: 97%;
    background: rgb(33, 33, 33);
    left: 0;
    display: flex;
    /* border-radius: 3px 24px 24px 24px; */
    border-radius: 24px;
}

.title {
    color: white;
    font-size: 22px;
}

.content {
    margin-left: 15px;
}
@media (max-width: 700px) {
    .title {
        display: none;
    }
    .chat {
        width: 60px;
    }
}

.selected {
    background: rgb(75, 74, 72);
}

.unread {
    position: absolute;
    background: red;
    color: white;
    border-radius: 50%;
    font-size: 14px;
    padding: 3px;
    min-width: 25px;
    min-height: 25px;
    max-height: 25px;
    max-width: 25px;
    margin-left: 10px;
    vertical-align: center;
    text-align: center;
    left: 0;
    -ms-user-select: none;
    -moz-user-select: none;
    -webkit-user-select: none;
    user-select: none;
}
.none-unread {
    display: none;
}
.chat:hover {
    background: rgb(95, 91, 91);
}
.avatar {
    max-height: 50px;
    max-width: 50px;
    min-height: 50px;
    min-width: 50px;
    vertical-align: center;
    margin-top: 5px;
    margin-left: 5px;
    border-radius: 50%;
}
.lastMessage {
    color: rgb(204, 197, 197) !important;
}
.my_unread {
    position: absolute;
    right: 13px;
}
.online {
    content: " ";
    border-radius: 50%;
    background: #2fbd2f;
    position: absolute;
    height: 10px;
    width: 10px;
    margin-left: 42px;
    margin-top: 42px;
}
</style>
