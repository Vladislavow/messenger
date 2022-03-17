<template>
    <div
        :class="{
            received: userId == message.recipient,
            message,
        }"
    >
        {{ message.content }}
        {{ message.created_at }}
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
    left: 63%;
    background: blue;
    width: 35%;
    border-radius: 15px;
    padding: 5px;
    margin: 10px;
}
.received {
    left: 0;
    background: gold;
}
</style>
