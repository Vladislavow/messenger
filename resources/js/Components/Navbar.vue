<template>
    <div class="navi">
        <v-btn @click="logout">Log out</v-btn>
    </div>
</template>

<script>
export default {
    methods: {
        logout() {
            this.$store.dispatch("logout").then(() => {
                this.$router.push("/login");
            });
        },
    },
};
</script>

<style scoped>
.navi {
    position: fixed !important;
    height: 8%;
    width: 25%;
    top: 0;
    left: 0;
    background: rgb(33, 33, 33);
    border-bottom: 3px solid rgb(0, 0, 0);
}
</style>
