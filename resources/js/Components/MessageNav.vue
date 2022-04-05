<template>
  <div @click="showProfile()" class="navbar">
    <v-btn
      dark
      fab
      small
      depressed
      class="close-btn"
      @click.prevent="closeBoard"
    >
      <v-icon> mdi-arrow-left </v-icon>
    </v-btn>
    <v-img v-if="chat" class="avatar" :src="chat.avatar">
      <template v-slot:placeholder>
        <v-row class="fill-height ma-0" align="center" justify="center">
          <v-progress-circular
            indeterminate
            color="grey lighten-5"
          ></v-progress-circular>
        </v-row>
      </template>
    </v-img>
    <div class="information">
      <div v-if="chat" class="title">
        {{ chat.firstname + " " + chat.lastname }}
      </div>
      <div class="online">
        {{
          chat.online ? "Online" : chat.last_seen ? chat.last_seen : "Offline"
        }}
      </div>
    </div>
  </div>
</template>

<script>
export default {
  methods: {
    showProfile() {
      if (this.chat) {
        this.$store.dispatch("setSelectedProfile", this.chat.id);
      }
    },
    closeBoard() {
        this.$store.dispatch("selectChat", this.chat.id);
    },
  },
  computed: {
    chat: function () {
      return this.$store.getters.selectedChatInfo;
    },
  },
};
</script>

<style scoped>
.navbar {
  min-height: 58px;
  position: fixed;
  width: 50%;
  background: rgba(33, 33, 33, 0.9);
  margin: 0;
  top: 0;
  padding: 4px 15px;
  border-left: 1px solid balck;
  border-bottom: 2px solid black;
  display: flex;
  cursor: pointer;
  z-index: 800;
  height: 40px;
}
.avatar {
  max-height: 50px;
  max-width: 50px;
  min-height: 50px;
  min-width: 50px;
  vertical-align: center;
  margin-left: 5px;
  border-radius: 50%;
}

.information {
  margin-left: 20px;
  display: flex;
  color: white;
  flex-direction: column;
}
.online {
  font-size: 14px;
}
.close-btn {
  align-self: center;
}
</style>
