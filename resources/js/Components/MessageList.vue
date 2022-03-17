<template>
    <div class="messages" id="mes">
        <div v-for="(message, index) in this.messages" :key="index">
            <message :message="message" :userId="userId" />
        </div>
    </div>
</template>

<script>
import Message from "./Message.vue";
export default {
    components: { Message },
    props: ["messages", "userId"],
    watch: {
        messages: function () {
            setTimeout(() => {
                var container = document.getElementById("mes");
                container.scrollTop =
                    container.scrollHeight - container.clientHeight;
            }, 50);
        },
    },
};
</script>

<style scoped>
.messages {
    position: fixed;
    width: 50%;
    height: 85%;
    background: grey;
    display: inline;
    overflow: scroll;
    overflow-x: hidden;
}
</style>
