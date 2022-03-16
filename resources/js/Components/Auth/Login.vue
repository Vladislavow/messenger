<template>
    <div class="back">
        <v-card color="red" class="login">
            <v-text-field v-model="user.email" prepend-inner-icon="mdi-email" />
            <v-text-field
                v-model="user.password"
                prepend-inner-icon="mdi-lock"
            />
            <v-btn dark @click.prevent="login"> Enter</v-btn>
            <router-link to="/register">
                <v-btn> Sign up</v-btn>
            </router-link>
        </v-card>
    </div>
</template>

<script>
export default {
    data: () => {
        return {
            user: {
                email: "awd@awd.awd",
                password: "12345678",
            },
        };
    },
    methods: {
        login() {
            this.$store
                .dispatch("login", this.user)
                .then(() => {
                    this.$router.push("/");
                })
                .catch((error) => {
                    console.log(error);
                });
        },
    },
};
</script>

<style scoped>
.login {
    width: 40%;
    position: fixed;
    border-radius: 10%;
    padding: 15px;
    text-align: center;
    min-width: 300px;
    min-height: 200px;
    max-height: 200px;
    /* background: rgba(255, 255, 255, 0.24);
    border-radius: 16px;
    box-shadow: 0 4px 30px rgba(0, 0, 0, 0.1);
    backdrop-filter: blur(6.1px);
    -webkit-backdrop-filter: blur(6.1px);
    border: 1px solid rgba(255, 255, 255, 0.41); */
}
.back {
    background: black;
    position: fixed;
    width: 100%;
    height: 100%;
    display: flex;
    justify-content: center;
    align-items: center;
}
</style>
