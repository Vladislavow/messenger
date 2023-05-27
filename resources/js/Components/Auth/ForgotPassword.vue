<template>
    <div class="forgot-password">
        <div class="image-container">
            <img src="/images/forgot-password.svg" alt="Login"/>
        </div>
        <div class="fp-container">
            <div class="fp-form" v-if="!sent">
                <h2>Forgot your password?</h2>
                <h4 style="text-align: left; margin-top: 10px">
                    Enter your email for recovery
                </h4>
                <br/>
                <v-text-field
                    v-model="email"
                    placeholder="Email"
                    solo
                    prepend-inner-icon="mdi-email"
                    readonly
                    onfocus="this.removeAttribute('readonly')"
                    :error="this.errors != null && this.errors.email != null"
                    :error-messages="this.errors.email"
                ></v-text-field>
                <v-btn
                    @click="resetPassword"
                    block
                    color="primary"
                    :loading="loading"
                >Send reset link
                </v-btn
                >
                <div class="sign-in">
                    Back to
                    <router-link to="/login">sign in</router-link>
                </div>
            </div>
            <div class="fp-form" v-if="sent">
                <h2>Link has been sent to your email</h2>
                <router-link to="/login">Back to sign in</router-link>
                <br/>
            </div>
        </div>
    </div>
</template>

<script>
export default {
  data: () => {
    return {
      email: null,
      loading: false,
      sent: false,
      errors: {},
    };
  },
  methods: {
      resetPassword() {
      this.errors = {};
      this.loading = true;
      axios
        .post("/api/forgot-password", { email: this.email })
        .then((resp) => {
          this.sent = true;
        })
        .catch((err) => {
          if (err.response.status == 422) {
            this.errors = err.response.data.errors;
          } else {
          }
        })
        .finally(() => {
          this.loading = false;
        });
    },
  },
};
</script>

<style lang="scss" scoped>
.forgot-password {
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

    .fp-container {
        display: flex;
        align-items: center;
        width: 40%;
        height: 100%;
        opacity: 0.8;

        .fp-form {
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

        .sign-in {
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
        .fp-container {
            width: 100%;
            height: unset !important;

            .fp-form {
                width: 100%;
            }
        }
    }
}
</style>
