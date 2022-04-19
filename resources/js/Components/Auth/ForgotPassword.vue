<template>
  <div class="forgot">
    <div class="reset-container">
      <div v-if="!sent" class="form">
        <div class="information">Forgot password?</div>
        <p class="add-text">Enter your email for reset password</p>
        <div class="inputs">
          <v-text-field
            v-model="email"
            prepend-inner-icon="mdi-email"
            dark
            label="Email"
            :error="this.errors != null && this.errors.email != null"
            :error-messages="this.errors.email"
          >
          </v-text-field>
          <v-btn :loading="loading" @click.prevent="sendResetMail"
            >Send mail</v-btn
          >
          <router-link to="/login">
            <v-btn>Log in</v-btn>
          </router-link>
        </div>
      </div>
      <div v-if="sent" class="sent">
        <div>We have emailed your password reset link!</div>
        <router-link to="/login">
          <v-btn>Log in</v-btn>
        </router-link>
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
    sendResetMail() {
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
.forgot {
  height: 100%;
  width: 100%;
  background: rgb(58, 56, 83);
  display: flex;
  align-items: center;
  justify-content: center;
  background-image: url("https://blog.1a23.com/wp-content/uploads/sites/2/2020/02/pattern-7.svg");
}

.form {
  display: flex;
  align-items: center;
  padding: 20px;
  flex-direction: column;
}
.reset-container {
  font-size: 23px;
  color: white;
  height: 230px;
  width: 33%;
  min-width: 200px;
  background: rgba(240, 240, 240, 0.33);
  border-radius: 35px;
  box-shadow: 0 4px 30px rgba(0, 0, 0, 0.1);
  backdrop-filter: blur(6.1px);
  -webkit-backdrop-filter: blur(6.1px);
  border: 1px solid rgba(255, 255, 255, 0.99);
  @media (max-width: 1000px) {
    width: 75%;
    height: 230px;
  }
  @media (max-width: 400px) {
    width: 80%;
    height: 230px;
  }
}
.add-text {
  align-self: flex-start !important;
  font-size: 18px;
  margin-bottom: 0px;
}
.sent {
  display: flex;
  align-items: center;
  justify-content: center;
  flex-direction: column;
  height: 100%;
  font-size: 28px;
}
.inputs {
  width: 100%;
  text-align: center;
  .v-btn {
    margin-top: 15px;
  }
}
</style>