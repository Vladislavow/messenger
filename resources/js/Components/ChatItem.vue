<template>
    <div>
        <div :class="{ selected: this.selected, chat }" @click="selectChat">
            <div class="title">{{ chat.firstname + " " + chat.lastname }}</div>
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
    border-radius: 10px;
    margin: 5px;
    width: 97%;
    background: rgb(82, 77, 77);
    left: 0;
}

.title {
    color: white;
    margin-left: 15px;
    font-size: 22px;
}

.selected {
    background: gold;
}
</style>
