<template>
  <div class="forgot">
    <div class="reset-container">
      <div class="form">
        <div class="information">Reset password</div>
        <p class="add-text">Enter new password:</p>
        <div class="inputs">
          <v-text-field
            v-model="password"
            prepend-inner-icon="mdi-lock"
            dark
            :type="show ? 'text' : 'password'"
            label="Password"
            :error="this.errors != null && this.errors.password != null"
            :error-messages="this.errors.password"
            :append-icon="show ? 'mdi-eye-off':'mdi-eye'"
            @click:append="show = !show"
          />
          <v-text-field
            v-model="confirm_password"
            prepend-inner-icon="mdi-lock"
            dark
            label="Confirm password"
            :error="this.errors != null && this.errors.confirm_password != null"
            :error-messages="this.errors.confirm_password"
            :append-icon="show1 ? 'mdi-eye-off':'mdi-eye'"
            @click:append="show1 = !show1"
            :type="show1 ? 'text' : 'password'"
          />
          <v-btn :loading="loading" @click="resetPassword">Save password</v-btn>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  data: () => {
    return {
      token: null,
      password: null,
      email: null,
      confirm_password: null,
      loading: false,
      errors: {},
      show:false,
      show1:false,
    };
  },
  mounted() {
    if (this.$route.query.token) {
      this.token = this.$route.query.token;
    } else {
      this.$router.push("/login");
    }
    if (this.$route.query.email) {
      this.email = this.$route.query.email;
    } else {
      this.$router.push("/login");
    }
  },
  methods: {
    resetPassword() {
      this.errors = {};
      if (this.password && this.confirm_password) {
        if (this.password == this.confirm_password) {
          this.loading = true;
          axios
            .post("/api/reset-password", {
              email: this.email,
              token: this.token,
              password: this.password,
              password_confirmation: this.confirm_password,
            })
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
          this.errors.confirm_password = "Password don't match";
        }
      } else {
        if (!this.password) {
          this.errors.password = "Password required";
        }
        if (!this.confirm_password) {
          this.errors.confirm_password = "Password confirm required";
        }
      }
    },
  },
};
</script>

<style lang="scss" scoped>
.forgot {
  height: 100%;
  width: 100%;
  background: rgb(109, 94, 94);
  display: flex;
  align-items: center;
  justify-content: center;
  background-image: url('https://blog.1a23.com/wp-content/uploads/sites/2/2020/02/pattern-33.svg');
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
  width: 33%;
  min-width: 200px;
  background: rgba(240, 240, 240, 0.14);
  border-radius: 35px;
  box-shadow: 0 4px 30px rgba(0, 0, 0, 0.1);
  backdrop-filter: blur(6.1px);
  -webkit-backdrop-filter: blur(6.1px);
  border: 1px solid rgba(255, 255, 255, 1);
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