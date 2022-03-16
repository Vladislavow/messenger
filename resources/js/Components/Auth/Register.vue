<template>
    <div class="back">
        <v-card class="register">
            <v-text-field
                label="Firstname"
                v-model="user.firstname"
            ></v-text-field>
            <v-text-field
                label="Lastname"
                v-model="user.lastname"
            ></v-text-field>
            <v-text-field label="Email" v-model="user.email"></v-text-field>
            <v-text-field
                label="Nickname"
                v-model="user.nickname"
            ></v-text-field>
            <v-text-field label="Phone" v-model="user.phone"></v-text-field>
            <v-text-field
                label="Password"
                v-model="user.password"
                :type="show ? 'text' : 'password'"
                :append-icon="show ? 'mdi-eye-off' : 'mdi-eye'"
                @click:append="show = !show"
            ></v-text-field>
            <v-btn @click.prevent="register">Create account</v-btn>
            <router-link to="/login">
                <v-btn> Sign in</v-btn>
            </router-link>
        </v-card>
    </div>
</template>

<script>
export default {
    data: () => {
        return {
            user: {
                firstname: "",
                lastname: "",
                email: "",
                nickname: "",
                phone: "",
                password: "",
            },
            errors: {},
            show: false,
        };
    },
    methods: {
        register() {
            this.$store
                .dispatch("register", this.user)
                .then(() => {
                    this.$router.push("/");
                })
                .catch((error) => {
                    console.log(error);
                    this.errors = error.response.data;
                });
        },
    },
};
</script>

<style scoped>
.register {
    width: 40%;
    position: fixed;
    border-radius: 10%;
    padding: 15px;
    text-align: center;
    min-width: 300px;
    min-height: 200px;
    max-width: 500px;
    max-height: 600px;
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
