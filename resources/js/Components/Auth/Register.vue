<template>
    <div class="register">
        <div class="image-container">
            <img src="/images/signup.svg" alt="Login"/>
        </div>
        <div class="register-container">
            <div class="register-form">
                <v-text-field
                    solo
                    validate-on-blur
                    label="Firstname"
                    v-model="user.firstname"
                    prepend-icon="mdi-passport"
                    :error="this.errors != null && this.errors.firstname != null"
                    :error-messages="this.errors.firstname"
                ></v-text-field>
                <v-text-field
                    solo
                    validate-on-blur
                    label="Lastname"
                    v-model="user.lastname"
                    prepend-icon="mdi-passport"
                    :error="this.errors != null && this.errors.lastname != null"
                    :error-messages="this.errors.lastname"
                ></v-text-field>
                <v-text-field
                    solo
                    validate-on-blur
                    label="Phone"
                    prepend-icon="mdi-phone"
                    v-model="user.phone"
                    :error="this.errors != null && this.errors.phone != null"
                    :error-messages="this.errors.phone"
                ></v-text-field>
                <v-text-field
                    solo
                    validate-on-blur
                    label="Nickname"
                    v-model="user.nickname"
                    prepend-icon="mdi-at"
                    :error="this.errors != null && this.errors.nickname != null"
                    :error-messages="this.errors.nickname"
                ></v-text-field>
                <v-file-input
                    solo
                    validate-on-blur
                    label="Avatar"
                    v-model="user.avatar"
                    :error="this.errors != null && this.errors.avatar != null"
                    :error-messages="this.errors.avatar"
                />
                <div>
                    <v-menu
                        ref="menu"
                        v-model="menu"
                        transition="scale-transition"
                        offset-y
                        min-width="auto"
                        :close-on-content-click="false"
                    >
                        <template v-slot:activator="{ on, attrs }">
                            <v-text-field
                                solo
                                validate-on-blur
                                v-model="user.birthdate"
                                label="Birthdate"
                                v-bind="attrs"
                                v-on="on"
                                readonly
                                prepend-icon="mdi-calendar"
                                :error="errors != null && errors.birthdate != null"
                                :error-messages="errors.birthdate"
                            ></v-text-field>
                        </template>
                        <v-date-picker
                            solo
                            validate-on-blur
                            v-model="user.birthdate"
                            :active-picker.sync="activePicker"
                            :max="
              new Date(Date.now() - new Date().getTimezoneOffset() * 60000)
                .toISOString()
                .substr(0, 10)
            "
                            min="1950-01-01"
                            @change="save"
                        ></v-date-picker>
                    </v-menu>
                </div>
                <v-text-field
                    solo
                    validate-on-blur
                    prepend-icon="mdi-email"
                    label="Email"
                    v-model="user.email"
                    :error="this.errors != null && this.errors.email != null"
                    :error-messages="this.errors.email"
                    readonly
                    onfocus="this.removeAttribute('readonly')"
                >
                </v-text-field>
                <v-text-field
                    readonly
                    onfocus="this.removeAttribute('readonly')"
                    solo
                    validate-on-blur
                    label="Password"
                    v-model="user.password"
                    :type="show ? 'text' : 'password'"
                    :append-icon="show ? 'mdi-eye-off' : 'mdi-eye'"
                    @click:append="show = !show"
                    prepend-icon="mdi-lock"
                    :error="this.errors != null && this.errors.password != null"
                    :error-messages="this.errors.password"
                    autocomplete="new--password"
                ></v-text-field>
                <div class="btns">
                    <v-btn
                        block
                        color="primary"
                        @click.prevent="register">Sign up</v-btn>

                    <div class="sign-in">
                        Already have an account?
                        <router-link to="/login">Sign in </router-link>
                    </div>
                </div>
            </div>
        </div>
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
                avatar: undefined,
                birthdate: null,
            },
            errors: {},
            show: false,
            activePicker: null,
            date: null,
            menu: false,
        };
    },
    methods: {
        register() {
            const config = {"content-type": "multipart/form-data"};
            const formData = new FormData();
            formData.append("firstname", this.user.firstname);
            formData.append("lastname", this.user.lastname);
            formData.append("email", this.user.email);
            formData.append("nickname", this.user.nickname);
            formData.append("phone", this.user.phone);
            formData.append("password", this.user.password);
            formData.append("avatar", this.user.avatar);
            formData.append("birthdate", this.user.birthdate);
            this.$store
                .dispatch("register", {form: formData, config: config})
                .then(() => {
                    this.$router.push("/");
                })
                .catch((error) => {
                    this.errors = error.response.data.errors;
                });
        },
        save(date) {
            this.$refs.menu.save(date);
        },
    },
};
</script>

<style lang="scss" scoped>
.register {
    width: 100%;
    height: 100vh;
    background: rgb(248, 250, 251);
    display: flex;
    justify-content: center;
    align-items: center;

    .image-container {
        display: flex;
        justify-content: center;
        align-items: center;

        img {
            max-height: 70%;
        }

        width: 60%;
        height: 100%;
    }

    .register-container {
        display: flex;
        align-items: center;
        width: 40%;
        height: 100%;
        opacity: 0.8;

        .register-form {
            align-self: center;
            width: 80%;
            padding: 20px;
            text-align: center;
            @media (max-width: 1000px) {
                width: auto;
            }
        }

        .sign-in {
            margin: 15px 0px;
            opacity: 0.7;
        }
    }

    @media screen and (max-width: 1000px) {
        flex-direction: column;
        height: 100%;
        .image-container {
            max-height: 250px;
            width: 100%;
        }
        .register-container {
            width: 100%;
            height: unset !important;

            .register-form {
                width: 100%;
            }
        }
    }
}
</style>
