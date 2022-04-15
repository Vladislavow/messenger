<template>
  <div class="back">
    <div class="login">
      <v-text-field
        dark
        v-model="user.email"
        prepend-inner-icon="mdi-email"
        label="Email"
        :error="this.errors != null && this.errors.email != null"
        :error-messages="this.errors.email"
        readonly
        onfocus="this.removeAttribute('readonly')"
        @keyup.enter.prevent="$refs.password.focus()"
      />
      <v-text-field
        dark
        label="Password"
        v-model="user.password"
        prepend-inner-icon="mdi-lock"
        :error="this.errors != null && this.errors.password != null"
        :type="show ? 'text' : 'password'"
        :error-messages="this.errors.password"
        :append-icon="this.show ? 'mdi-eye' : 'mdi-eye-off'"
        @click:append="show = !show"
        readonly
        onfocus="this.removeAttribute('readonly')"
        @keypress.enter="login"
        ref="password"
      />
      <v-btn dark @click.prevent="login" :loading="loading"> Enter</v-btn>
      <router-link to="/register">
        <v-btn> Sign up</v-btn>
      </router-link>
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
      this.user.browser = this.getBrowserName()
       this.$store.dispatch("login", this.user)
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
  width: 40%;
  position: fixed;
  border-radius: 10%;
  padding: 15px;
  text-align: center;
  min-width: 300px;
  min-height: 200px;
  max-height: 200px;
  background: rgba(255, 255, 255, 0.24);
  border-radius: 16px;
  box-shadow: 0 4px 30px rgba(0, 0, 0, 0.1);
  backdrop-filter: blur(6.1px);
  -webkit-backdrop-filter: blur(6.1px);
  border: 1px solid rgba(255, 255, 255, 0.41);
}
.back {
  background-image: url("https://blog.1a23.com/wp-content/uploads/sites/2/2020/02/Desktop.png");
  background-size: cover;
  background-color: rgb(33, 33, 33);
  background-attachment: fixed;
  position: fixed;
  width: 100%;
  height: 100%;
  display: flex;
  justify-content: center;
  align-items: center;
}
</style>
