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
        />
        <v-btn fab dark @click="send">
            <v-icon>mdi-send</v-icon>
        </v-btn>
    </div>
</template>

<script>
export default {
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
    background: white;
    padding: 10px;
}
</style>
