<template>
  <div>
    <div :class="{ selected: this.selected, chat }" @click="selectChat">
      <v-img class="avatar" :src="chat.avatar">
        <template v-slot:placeholder>
          <v-row class="fill-height ma-0" align="center" justify="center">
            <v-progress-circular
              indeterminate
              color="grey lighten-5"
            ></v-progress-circular>
          </v-row>
        </template>
      </v-img>
      <div class="content">
        <div class="title">{{ getTitle() }}</div>
        <div v-if="chat.last_message && !chat.typing" class="lastMessage">
          {{ getLastMessage() }}
          <v-icon
            v-if="chat.last_message.attachments.length > 0"
            color="rgb(204, 197, 197)"
            >mdi-attachment</v-icon
          >
        </div>
        <div v-if="chat.typing" class="lastMessage">Typing...</div>
      </div>
      <div v-if="chat.last_message && myMessage()" class="my_unread">
        <v-icon small color="white">{{
          chat.last_message.read ? "mdi-check-all" : "mdi-check"
        }}</v-icon>
      </div>
      <div
        :class="{
          unread: chat.unread > 0,
          'none-unread': chat.unread <= 0,
        }"
      >
        {{ chat.unread > 99 ? "99+" : chat.unread }}
      </div>
      <div class="online" v-if="chat.online == true"></div>
    </div>
  </div>
</template>

<script>
export default {
  props: ["chat"],
  methods: {
    selectChat() {
      this.$store.dispatch("selectChat", this.chat.id);
    },
    getLastMessage() {
      if (this.chat.last_message.content) {
        return this.chat.last_message.content.length > 35
          ? this.chat.last_message.content.substr(0, 35) + "..."
          : this.chat.last_message.content;
      }
    },
    getTitle() {
      let name = this.chat.firstname + " " + this.chat.lastname;
      return name.length > 17 ? name.substr(0, 17) + "..." : name;
    },
    myMessage() {
      return this.chat.last_message.sender == localStorage.getItem("userid");
    },
  },
  computed: {
    selected: function () {
      return this.$store.getters.selectedChat == this.chat.id;
    },
  },
};
</script>

<style scoped>
.chat {
  min-height: 60px;
  margin: 10px;
  width: 97%;
  background: rgb(33, 33, 33);
  left: 0;
  display: flex;
  border-radius: 24px;
  transition: transform 1s;
}

.title {
  color: white;
  font-size: 22px;
}

.content {
  margin-left: 15px;
}

.selected {
  background: rgb(31, 53, 77);
}

.unread {
  position: absolute;
  background: red;
  color: white;
  border-radius: 50%;
  font-size: 14px;
  padding: 3px;
  min-width: 25px;
  min-height: 25px;
  max-height: 25px;
  max-width: 25px;
  margin-left: 10px;
  vertical-align: center;
  text-align: center;
  left: 0;
  -ms-user-select: none;
  -moz-user-select: none;
  -webkit-user-select: none;
  user-select: none;
}
.none-unread {
  display: none;
}
.chat:hover {
  background: rgb(31, 53, 77);
  -webkit-box-shadow: 1px 1px 18px -2px rgba(31, 53, 77, 0.73);
  box-shadow: 1px 1px 18px -2px rgba(31, 53, 77, 0.73);
}
.avatar {
  max-height: 50px;
  max-width: 50px;
  min-height: 50px;
  min-width: 50px;
  vertical-align: center;
  margin-top: 5px;
  margin-left: 5px;
  border-radius: 50%;
}
.lastMessage {
  color: rgb(204, 197, 197) !important;
}
.my_unread {
  position: absolute;
  right: 13px;
}
.online {
  content: " ";
  border-radius: 50%;
  background: #2fbd2f;
  position: absolute;
  height: 10px;
  width: 10px;
  margin-left: 42px;
  margin-top: 42px;
}
</style>
