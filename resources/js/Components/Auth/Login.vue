<template>
    <div class="login">
        <div class="image-container">
            <img src="/images/login.svg" alt="Login" />
        </div>
        <div class="login-container">
            <div class="login-form">
                <div class="input-container">
                    <h2>Sign in</h2>
                    <br />
                    <v-text-field
                        v-model="user.email"
                        placeholder="Email"
                        solo
                        prepend-inner-icon="mdi-email"
                        :error="
                            this.errors != null && this.errors.email != null
                        "
                        :error-messages="this.errors.email"
                    ></v-text-field>
                    <v-text-field
                        :type="show ? 'text' : 'password'"
                        v-model="user.password"
                        solo
                        hide-details=""
                        placeholder="Password"
                        prepend-inner-icon="mdi-lock"
                        :append-icon="show ? 'mdi-eye-off' : 'mdi-eye'"
                        @click:append="show = !show"
                        :error="
                            this.errors != null && this.errors.password != null
                        "
                        @keypress.enter="login"
                        :error-messages="this.errors.password"
                    ></v-text-field>
                    <div class="forgot-passowrd-link">
                        <router-link to="/forgot-password"
                        ><u>Forgot Password</u></router-link
                        >
                    </div>
                </div>
                <v-btn @click="login" :loading="loading" block color="primary"> Login</v-btn>
                <div class="sign-up">
                    Dont have an account?
                     <router-link to="/register">Sign up now</router-link>
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
        email: "",
        password: "",
      },
      errors: {},
      loading: false,
      show: false,
    };
  },
  methods: {
    login() {
      this.errors = {};
      this.loading = true;
      this.user.browser = this.getBrowserName();
      this.$store
        .dispatch("login", this.user)
        .then(() => {
          this.$router.push("/");
        })
        .catch((error) => {
          if (error.response.status == 422) {
            this.errors = error.response.data.errors;
          }
          if (error.response.status == 404) {
            this.errors = { email: "Invalid credentials" };
          }
          this.loading = false;
        });
    },
    getBrowserName() {
      if (
        (navigator.userAgent.indexOf("Opera") ||
          navigator.userAgent.indexOf("OPR")) != -1
      ) {
        return "Opera";
      } else if (navigator.userAgent.indexOf("Chrome") != -1) {
        return "Chrome";
      } else if (navigator.userAgent.indexOf("Safari") != -1) {
        return "Safari";
      } else if (navigator.userAgent.indexOf("Firefox") != -1) {
        return "Firefox";
      } else if (
        navigator.userAgent.indexOf("MSIE") != -1 ||
        !!document.documentMode == true
      ) {
        return "IE"; //crap
      } else {
        return "Unknown";
      }
    },
  },
};
</script>

<style lang="scss" scoped>
.login {
    width: 100%;
    height: 100vh;
    display: flex;
    background: rgb(248, 250, 251);
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
    .login-container {
        display: flex;
        align-items: center;
        width: 40%;
        height: 100%;
        opacity: 0.8;

        .login-form {
            // box-shadow: 0 4px 30px rgba(0, 0, 0, 0.1);
            backdrop-filter: blur(6.1px);
            -webkit-backdrop-filter: blur(6.1px);
            align-self: center;
            width: 80%;
            padding: 20px;
            text-align: center;
            @media (max-width: 1000px) {
                width: auto;
            }
            .forgot-passowrd-link {
                text-align: right;
                margin: 10px 0px;
            }
        }
        .sign-up {
            margin: 15px 0px;
            opacity: 0.7;
        }
    }
    @media screen and (max-width: 1000px) {
        flex-direction: column;
        .image-container {
            max-height: 250px;
            width: 100%;
        }
        .login-container {
            width: 100%;
            height: unset !important;
            .login-form {
                width: 100%;
            }
        }
    }
}
</style>
