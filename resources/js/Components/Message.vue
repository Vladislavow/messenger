<template>
    <div
        :class="{
            received: userId == message.recipient,
            message,
        }"
    >
        <div class="content">
            {{ message.content }}
            <sub class="time">
                {{
                    ("0" + new Date(message.created_at).getHours()).slice(-2) +
                    ":" +
                    ("0" + new Date(message.created_at).getHours()).slice(-2)
                }}
            </sub>
            <sub class="read">
                <v-icon small v-if="userId != message.recipient">{{
                    message.read == true ? "mdi-check-all" : "mdi-check"
                }}</v-icon>
            </sub>
        </div>
    </div>
</template>

<script>
export default {
    props: ["message", "userId"],
};
</script>

<style lang="scss" scoped>
.message {
    display: inline-block;
    position: relative;
    /* left: 80%; */
    background: rgb(6, 153, 13);
    max-width: 70%;
    border-radius: 15px;
    padding: 5px;
    margin: 10px;
    vertical-align: right;
    align-self: flex-end;
}
.content {
    word-wrap: break-word;
    margin-left: 5px;
    margin-right: 5px;
    font-size: 22px;
}
.received {
    align-self: flex-start;
    text-align: left;
    left: 0;
    background: rgb(255, 255, 255);
    &:after {
        border-bottom-color: white !important;
        left: 20px;
    }
}

.time {
    position: inline;
    justify-self: right;
    font-size: 14px;
}

.message::after {
    bottom: 100%;
    right: 20px;
    border: solid transparent;
    content: " ";
    height: 0;
    width: 0;
    position: absolute;
    pointer-events: none;
    border-bottom-color: rgb(6, 153, 13);
    border-width: 10px;
}
</style>
