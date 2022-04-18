<template>
  <div class="back">
    <div elevation="8" rounded="" class="register">
      <v-text-field
        label="Firstname"
        v-model="user.firstname"
        prepend-icon="mdi-passport"
        :error="this.errors != null && this.errors.firstname != null"
        :error-messages="this.errors.firstname"
        dark
      ></v-text-field>
      <v-text-field
        dark
        label="Lastname"
        v-model="user.lastname"
        prepend-icon="mdi-passport"
        :error="this.errors != null && this.errors.lastname != null"
        :error-messages="this.errors.lastname"
      ></v-text-field>
      <v-text-field
        dark
        label="Phone"
        prepend-icon="mdi-phone"
        v-model="user.phone"
        :error="this.errors != null && this.errors.phone != null"
        :error-messages="this.errors.phone"
      ></v-text-field>
      <v-text-field
        dark
        label="Nickname"
        v-model="user.nickname"
        prepend-icon="mdi-at"
        :error="this.errors != null && this.errors.nickname != null"
        :error-messages="this.errors.nickname"
      ></v-text-field>
      <v-file-input
        dark
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
              dark
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
            dark
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
        dark
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
        dark
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
        <v-btn @click.prevent="register">Create account</v-btn>
        <router-link to="/login">
          <v-btn> Sign in</v-btn>
        </router-link>
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
      const config = { "content-type": "multipart/form-data" };
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
        .dispatch("register", { form: formData, config: config })
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

<style scoped>
.register {
  width: 40%;
  border-radius: 50px;
  padding: 15px;
  text-align: center;
  min-width: 300px;
  min-height: 200px;
  max-height: 630px;
  max-width: 500px;
  align-self: center;
  background: rgba(255, 255, 255, 0.24);
  border-radius: 16px;
  box-shadow: 0 4px 30px rgba(0, 0, 0, 0.1);
  backdrop-filter: blur(6.1px);
  -webkit-backdrop-filter: blur(6.1px);
  border: 1px solid rgba(255, 255, 255, 0.41);
}
.back {
  background-size: cover;
  background: rgb(58, 56, 83);
  background-image: url("https://web.telegram.org/k/assets/img/pattern.svg");
  background-attachment: fixed;
  display: flex;
  justify-content: center;
  bottom: 0;
  height: 100%;
  padding: 50px;
}
.error {
  border-radius: 10px;
  color: white;
}
.btns {
  margin-top: 10px;
}
</style>
