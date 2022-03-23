<template>
    <div @click="showProfile()" class="navbar">
        <img v-if="chat" class="avatar" :src="chat.avatar" />
        <div v-if="chat" class="title">
            {{ chat.firstname + " " + chat.lastname }}
        </div>
    </div>
</template>

<script>
export default {
    methods: {
        showProfile() {
            if (this.chat) {
                this.$store.dispatch("setSelectedProfile", this.chat.id);
            }
        },
    },
    computed: {
        chat: function () {
            return this.$store.getters.selectedChatInfo;
        },
    },
};
</script>

<style scoped>
.navbar {
    min-height: 58px;
    position: fixed;
    height: 8%;
    width: 50%;
    background: rgb(33, 33, 33);
    margin: 0;
    top: 0;
    padding: 4px 15px;
    border-left: 1px solid balck;
    border-bottom: 2px solid black;
    display: flex;
}
.avatar {
    height: 50px;
    width: 50px;
    border-radius: 50%;
}

.title {
    margin-left: 20px;
    color: white;
}
</style>
