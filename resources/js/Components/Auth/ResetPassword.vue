<template>
    <div class="reset-password">
        <div class="image-container">
            <img src="/images/reset-password.svg" alt="reset-password"/>
        </div>
        <div class="rp-container">
            <div class="rp-form">
                <h2>Reset password</h2>
                <br/>
                <v-text-field
                    label="New password"
                    v-model="resetForm.password"
                    solo
                    :append-icon="show.password ? 'mdi-eye-off' : 'mdi-eye'"
                    @click:append="show.password = !show.password"
                    :error="this.errors != null && this.errors.password != null"
                    :error-messages="this.errors.password"
                    :type="show.password ? 'text' : 'password'"
                ></v-text-field>
                <v-text-field
                    label="Password confirmation"
                    v-model="resetForm.password_confirmation"
                    solo
                    :append-icon="
                        show.password_confirmation ? 'mdi-eye-off' : 'mdi-eye'
                    "
                    @click:append="
                        show.password_confirmation = !show.password_confirmation
                    "
                    :type="show.password_confirmation ? 'text' : 'password'"
                    :error="
                        this.errors != null &&
                        this.errors.password_confirmation != null
                    "
                    :error-messages="this.errors.password_confirmation"
                ></v-text-field>
                <v-btn
                    @click="resetPassword"
                    :loading="loading"
                    block
                    color="primary"
                >
                    Reset password
                </v-btn
                >
            </div>
        </div>
    </div>
</template>

<script>
export default {
  data: () => {
    return {
        resetForm: {
            token: null,
            email: null,
            password: null,
            password_confirmation: null,
        },
        show: {
            password: false,
            password_confirmation: false,
        },
        loading: false,
        errors: {},
    };
  },
  mounted() {
    if (this.$route.query.token) {
      this.resetForm.token = this.$route.query.token;
    } else {
      this.$router.push("/login");
    }
    if (this.$route.query.email) {
      this.resetForm.email = this.$route.query.email;
    } else {
      this.$router.push("/login");
    }
  },
  methods: {
    resetPassword() {
      this.errors = {};
      if (this.resetForm.password && this.resetForm.password_confirmation) {
        if (this.resetForm.password == this.resetForm.password_confirmation) {
          this.loading = true;
          axios
            .post("/api/reset-password", this.resetForm)
            .then((resp) => {
              this.$toast.success(resp.data);
              this.$router.push("/login");
            })
            .catch((err) => {
              if (err) {
              }
            })
            .finally(() => {
              this.loading = false;
            });
        } else {
          this.errors.password_confirmation = "Passwords doesn't match";
        }
      } else {
        if (!this.password) {
          this.errors.password = "Password required";
        }
        if (!this.password_confirmation) {
          this.errors.password_confirmation = "Password confirm required";
        }
      }
    },
  },
};
</script>

<style lang="scss" scoped>
.reset-password {
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

    .rp-container {
        display: flex;
        align-items: center;
        width: 40%;
        height: 100%;
        opacity: 0.8;

        .rp-form {
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
        .rp-container {
            width: 100%;
            height: unset !important;

            .rp-form {
                width: 100%;
            }
        }
    }
}
</style>
