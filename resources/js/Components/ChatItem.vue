<template>
    <div>
        <div :class="{ selected: this.selected, chat }" @click="selectChat">
            <img class="avatar" :src="chat.avatar" />
            <div class="title">{{ chat.firstname + " " + chat.lastname }}</div>
            <div
                :class="{
                    unread: chat.unread > 0,
                    'none-unread': chat.unread <= 0,
                }"
            >
                {{ chat.unread > 99 ? "99+" : chat.unread }}
            </div>
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
    margin-left: 15px;
    font-size: 22px;
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
</style>
