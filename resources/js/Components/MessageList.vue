<template>
    <div class="messages" ref="container" id="mes">
        <!-- <infinite-loading  //to do:inifinite loading
            v-if="messages.length > 0 && page < totalPages + 1"
            @infinite="getMessages"
            direction="top"
            spinner="spiral"
            ref="infin"
            force-use-infinite-wrapper="true"
        /> -->
        <v-btn
            class="loadMore"
            v-if="messages.length > 0 && page < totalPages + 1"
            @click="getMessages"
            rounded
        >
            Load more
        </v-btn>
        <message
            v-for="(message, index) in this.messages"
            :key="index"
            :message="message"
            :userId="userId"
        />
    </div>
</template>

<script>
import Message from "./Message.vue";
export default {
    data: () => {
        return {
            scrollLock: false,
        };
    },
    components: { Message },
    props: ["messages", "userId", "laoding", "page", "totalPages"],
    watch: {
        messages: function () {
            setTimeout(() => {
                if (this.scrollLock) {
                    this.scrollLock = false;
                    return;
                }
                var container = document.getElementById("mes");
                container.scrollTop =
                    container.scrollHeight - container.clientHeight;
                this.$emit("loaded");
            }, 50);
        },
    },
    methods: {
        getMessages() {
            this.scrollLock = true;
            this.$emit("getMessages");
        },
    },
    mounted() {},
};
</script>

<style scoped>
.messages {
    position: fixed;
    width: 50%;
    height: 85%;
    background: grey;
    overflow: scroll;
    overflow-x: hidden;
    display: flex;
    flex-direction: column;
}
.loadMore {
    margin-top: 3px;
    max-width: 20%;
    box-shadow: none;
    align-self: center;
}
</style>
