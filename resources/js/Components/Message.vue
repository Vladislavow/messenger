<template>
    <div
        :class="{
            received: userId == message.recipient,
            message,
        }"
    >
        {{ message.content }}
        <p class="time">{{ message.created_at }}</p>
        <div class="read">
            <v-icon v-if="userId != message.recipient">{{
                message.read == true ? "mdi-check-all" : "mdi-check"
            }}</v-icon>
        </div>
    </div>
</template>

<script>
export default {
    props: ["message", "userId"],
};
</script>

<style scoped>
.message {
    display: inline-block;
    position: relative;
    /* left: 80%; */
    background: blue;
    max-width: 70%;
    border-radius: 15px;
    padding: 5px;
    margin: 10px;
    text-align: right;
    vertical-align: right;
    align-self: flex-end;
}
.received {
    align-self: flex-start;
    text-align: left;
    left: 0;
    background: gold;
}

.time {
    text-align: right;
    font-size: 14px;
}
</style>
