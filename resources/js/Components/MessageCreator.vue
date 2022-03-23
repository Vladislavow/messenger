<template>
    <div class="creator">
        <v-textarea
            no-resize
            filled
            placeholder="Message here"
            v-model="content"
            clearable
            rows="2"
            rounded
            dark
            @keyup.enter="keysHandling"
        />
        <v-btn
            :disabled="!this.content"
            :loading="this.loading"
            fab
            dark
            @click="send"
        >
            <v-icon color="white">mdi-send</v-icon>
        </v-btn>
    </div>
</template>

<script>
export default {
    props: ["loading"],
    data: () => {
        return {
            content: "",
        };
    },
    methods: {
        send() {
            if (this.content) {
                this.$emit("send", this.content);
                this.content = "";
            }
        },
        keysHandling(event) {
            if (event.shiftKey === true && event.key === "Enter") {
                return;
            }
            this.send();
        },
    },
};
</script>

<style scoped>
.creator {
    position: fixed;
    width: 50%;
    height: 15%;
    bottom: 0;
    display: flex;
    background: rgb(33, 33, 33);
    padding: 10px;
}
.v-btn {
    margin-left: 3px;
}
@media (max-width: 700px) {
    .creator {
        width: 61%;
    }
}
</style>
