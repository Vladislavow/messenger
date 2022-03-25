<template>
    <div
        :class="{
            received: userId == message.recipient,
            message,
        }"
        @contextmenu.prevent="show"
    >
        <div class="content">
            {{ message.content }}
            <div class="statuses">
                <span class="time">
                    {{
                        message.created_at
                            | moment("timezone", "Europe/Kiev", "hh:mm")
                    }}
                    <!-- {{
                        ("0" + new Date(message.created_at).getHours()).slice(
                            -2
                        ) +
                        ":" +
                        ("0" + new Date(message.created_at).getMinutes()).slice(
                            -2
                        )
                    }} -->
                </span>
                <span class="read">
                    <v-icon small v-if="userId != message.recipient">{{
                        message.read == true ? "mdi-check-all" : "mdi-check"
                    }}</v-icon>
                </span>
            </div>
        </div>
        <v-menu v-model="showMenu" :position-x="x" :position-y="y" offset-y>
            <v-list>
                <div @mouseleave="hide">
                    <v-list-item @click="deleteMessage"> Delete </v-list-item>
                </div>
            </v-list>
        </v-menu>
    </div>
</template>

<script>
export default {
    data: () => {
        return {
            showMenu: false,
            x: 0,
            y: 0,
        };
    },
    props: ["message", "userId"],
    methods: {
        show(e) {
            e.preventDefault();
            if (this.message.sender == localStorage.getItem("userid")) {
                this.showMenu = false;
                this.x = e.clientX;
                this.y = e.clientY;
                this.$nextTick(() => {
                    this.showMenu = true;
                });
            }
        },
        hide() {
            this.showMenu = false;
        },
        deleteMessage() {
            this.$emit("deleteMessage", this.message);
        },
    },
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
    -ms-user-select: none;
    -moz-user-select: none;
    -webkit-user-select: none;
    user-select: none;
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

.statuses {
    position: relative;
    bottom: -3px;
    float: right;
    margin-left: 3px;
    display: inline;
    -ms-user-select: none;
    -moz-user-select: none;
    -webkit-user-select: none;
    user-select: none;
}
</style>
