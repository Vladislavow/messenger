<template>
  <div class="profile">
    <div class="close">
      <v-icon @click.prevent="closeProfile" color="white" large>
        mdi-close
      </v-icon>
    </div>
    <div v-if="this.canUpdate" class="update-btn">
      <v-icon @click.prevent="switchUpdate" color="white" large>
        {{ !update ? "mdi-account-edit-outline" : "mdi-account-edit" }}
      </v-icon>
    </div>
    <input
      type="file"
      @change="updateAvatar"
      ref="file"
      style="display: none"
      accept=".png, .jpg, .jpeg"
    />
    <div class="img-container">
      <v-img
        contain
        class="image"
        :src="this.chat.avatar"
        max-height="40%"
        max-width="40%"
      >
        <template>
          <v-row class="fill-height ma-0" align="center" justify="center">
            <v-icon
              v-if="canUpdate"
              @click.prevent="$refs.file.click()"
              large
              class="edit-avatar"
              color="white"
            >
              mdi-camera</v-icon
            >
          </v-row>
        </template>
        <template v-slot:placeholder>
          <v-row class="fill-height ma-0" align="center" justify="center">
            <v-progress-circular
              indeterminate
              color="grey lighten-5"
            ></v-progress-circular>
          </v-row>
        </template>
      </v-img>
    </div>
    <v-divider dark />
    <div v-if="!this.update" class="name">
      {{ chat.firstname + " " + chat.lastname }}
    </div>
    <div v-if="!this.update" class="information">
      <div class="canCopy" @click="copyToClipbioard(chat.nickname)">
        <v-icon dark>mdi-at</v-icon>{{ " " + chat.nickname }}
      </div>
      <div class="canCopy" @click="copyToClipbioard(chat.email)">
        <v-icon dark>mdi-email-outline</v-icon>{{ " " + chat.email }}
      </div>
      <div class="canCopy" @click="copyToClipbioard(chat.phone)">
        <v-icon dark>mdi-phone-outline</v-icon>{{ " " + chat.phone }}
      </div>
      <div v-if="chat.birthdate">
        <v-icon dark>mdi-cake-variant-outline</v-icon>{{ " " + chat.birthdate }}
      </div>
      <div v-if="chat.bio">
        <v-icon dark>mdi-information-outline</v-icon>{{ " " + chat.bio }}
      </div>
    </div>
    <div v-if="this.update" class="update">
      <v-text-field
        prepend-icon="mdi-passport"
        label="Firstname"
        rounded
        outlined
        dark
        v-model="chat.firstname"
        :error="this.errors != null && this.errors.firstname != null"
        :error-messages="this.errors.firstname"
      >
      </v-text-field>
      <v-text-field
        prepend-icon="mdi-passport  "
        label="Lastname"
        rounded
        outlined
        dark
        v-model="chat.lastname"
        :error="this.errors != null && this.errors.lastname != null"
        :error-messages="this.errors.lastname"
      >
      </v-text-field>
      <v-text-field
        prepend-icon="mdi-at"
        label="Nickname"
        rounded
        outlined
        dark
        v-model="chat.nickname"
        :error="this.errors != null && this.errors.nickname != null"
        :error-messages="this.errors.nickname"
      >
      </v-text-field>
      <v-text-field
        prepend-icon="mdi-email-outline"
        label="Email"
        rounded
        outlined
        dark
        v-model="chat.email"
        :error="this.errors != null && this.errors.email != null"
        :error-messages="this.errors.email"
      >
      </v-text-field>
      <v-text-field
        prepend-icon="mdi-phone-outline"
        label="Phone"
        rounded
        outlined
        dark
        v-model="chat.phone"
        :error="this.errors != null && this.errors.phone != null"
        :error-messages="this.errors.phone"
      >
      </v-text-field>
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
              rounded
              outlined
              dark
              v-model="chat.birthdate"
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
            v-model="chat.birthdate"
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
      <v-textarea
        prepend-icon="mdi-information-outline"
        label="Bio"
        rounded
        outlined
        dark
        v-model="chat.bio"
        rows="2"
      >
      </v-textarea>
      <div class="buttons">
        <v-btn plain color="green" dark class="save" @click.prevent="updateUser"
          >Save<v-icon color="green">mdi-content-save</v-icon>
        </v-btn>
        <v-btn plain color="red" @click.prevent="closeUpdate"
          >Close <v-icon>close</v-icon></v-btn
        >
      </div>
    </div>
  </div>
</template>

<script>
export default {
  data: () => {
    return {
      chat: {},
      update: false,
      show: false,
      activePicker: null,
      date: null,
      menu: false,
      newAvatar: null,
      errors: {},
    };
  },
  watch: {
    chatId: function () {
      this.getChat();
    },
  },
  created() {
    this.getChat();
  },
  methods: {
    save(date) {
      this.$refs.menu.save(date);
    },
    copyToClipbioard(value) {
      navigator.clipboard.writeText(value).then(() => {
        if (value) {
          this.$toast.success("Copied to cliapborad");
        }
      });
    },
    getChat() {
      axios.get("/api/chat/" + this.chatId).then((resp) => {
        this.chat = resp.data;
      });
    },
    closeProfile() {
      this.$store.dispatch("setSelectedProfile", null);
    },
    showUpdate() {
      this.update = true;
    },
    closeUpdate() {
      this.getChat();
      this.$store.dispatch("getUser");
      this.update = false;
    },
    updateUser() {
      axios
        .put("/api/user", this.chat)
        .then((response) => {
          this.$toast.success(response.data);
          this.closeUpdate();
        })
        .catch((errors) => {
          if (errors.response.status == 422) {
            this.errors = errors.response.data.errors;
          }
        });
    },
    updateAvatar(e) {
      let avatar = e.target.files[0];
      if (avatar) {
        const config = { "content-type": "multipart/form-data" };
        const formData = new FormData();
        formData.append("avatar", avatar);
        axios.post("/api/user/avatar", formData, config).then((resp) => {
          this.closeUpdate();
          this.$toast.success(resp.data);
        });
      }
    },
    switchUpdate() {
      if (this.update) {
        this.closeUpdate();
      } else {
        this.showUpdate();
      }
    },
  },
  computed: {
    chatId: function () {
      return this.$store.getters.selectedProfile;
    },
    canUpdate: function () {
      return this.chatId == localStorage.getItem("userid");
    },
  },
};
</script>

<style lang="scss" scoped>
.img-container {
  margin-top: 10px;
}
.profile {
  position: fixed;
  width: 25%;
  height: 100%;
  right: 0;
  background: rgba(33, 33, 33, 0.9);
  border-left: 1px solid black;
  overflow: scroll;
  overflow-x: hidden;
  @media (max-width: 424px) {
    width: 100%;
    right: 0px;
    z-index: 999 !important;
     background: rgba(33, 33, 33, 1);
  }
}
.profile::-webkit-scrollbar {
  width: 5px;
  border-radius: 100px;
}
.profile::-webkit-scrollbar-thumb {
  background-color: white;
  display: none;
  border-radius: 5px;
}
.profile::-webkit-scrollbar-thumb:hover {
  display: initial;
}
.image {
  margin-left: 30%;
  margin-bottom: 10px;
  min-height: 50px;
}
.name {
  color: white;
  font-size: 30px;
  word-break: break-all;
  margin-left: 5px;
}
.information {
  color: white;
  font-size: 22px;
}
.information * {
  word-break: break-all;
  padding: 5px 7px;
  border-radius: 10px;
  margin: 2px;
}
.information *:hover {
  background: rgb(77, 73, 73);
}
.close {
  position: absolute;
  left: 10px;
  top: 10px;
}
.update {
  padding: 10px;
}
.update-btn {
  position: absolute;
  right: 10px;
  top: 10px;
}
.edit-avatar {
  display: none !important;
}
.image:hover .edit-avatar {
  display: initial !important;
}
.buttons {
  display: flex;
  justify-content: center;
}
.save {
  margin-right: 10px;
}
.canCopy {
  cursor: pointer;
}
</style>
