<template>
    <div>
        <div :class="{ selected: this.selected, chat }" @click="selectChat">
            <div class="title">{{ chat.firstname + " " + chat.lastname }}</div>
            <div
                :class="{
                    unread: chat.unread > 0,
                    'none-unread': chat.unread <= 0,
                }"
            >
                {{ chat.unread }}
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
    border: 1px solid #ffff;
    border-radius: 3px 24px 24px 24px;
}

.title {
    color: white;
    margin-left: 15px;
    font-size: 22px;
}

.selected {
    background: gold;
}

.unread {
    background: red;
    color: white;
    border-radius: 50%;
    font-size: 14px;
    padding: 5px;
    min-width: 25px;
    min-height: 25px;
    max-height: 25px;
    max-width: 25px;
    margin-left: 10px;
    vertical-align: center;
    text-align: center;
}
.none-unread {
    display: none;
}
</style>
