<template>
  <div
    @keypress.esc="closeBoard"
    :class="{ board: true, empty: !chat && !selectedProfile }"
  >
    <message-nav
      v-if="chat"
      :class="this.selectedProfile ? '' : 'closedProfile'"
    />
    <audio-player
      v-if="selectedAudio && chat"
      :class="{ closedProfile: !this.selectedProfile }"
    />
    <message-list
      ref="messageList"
      :class="{
        audio: selectedAudio,
        ms: !this.selectedProfile,
        'with-filles': this.withFiles,
      }"
      v-if="chat"
      :loading="this.loading"
      :messages="this.messages"
      :userId="userId"
      :page="this.page"
      :totalPages="this.totalPages"
      @loaded="stopLoading"
      @getMessages="getMessages"
      @deleteMessage="showDeleteDialog"
    />
    <message-creator
      :class="this.selectedProfile ? '' : 'ms'"
      v-if="chat"
      @send="send"
      :loading="this.loading"
      :withFiles="this.withFiles"
      @changeWithFiles="changeWithFiles"
      @updateMessage="updateMessage"
    />
    <profile v-if="selectedProfile" />
    <v-progress-circular
      v-if="loading && messages == 0"
      :class="{ loadwp: this.selectedProfile, load: true }"
      color="white"
      size="50"
      class="load"
      indeterminate
    >
    </v-progress-circular>
    <img
      class="pam"
      :src="`images/${theme}_back.svg`"
      alt="Kiwi standing on oval"
    />
    <v-dialog
      transition="dialog-bottom-transition"
      v-model="dialog"
      persistent
      max-width="290"
    >
      <v-card>
        <v-card-title class="text-h5"> Delete message? </v-card-title>

        <v-card-text> You will not be able to undo this action </v-card-text>

        <v-card-actions>
          <v-spacer></v-spacer>

          <v-btn color="green darken-1" text @click="dialog = false">
            Close
          </v-btn>

          <v-btn color="red darken-1" text @click="deleteMessage">
            Delete
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </div>
</template>

<script>
import MessageNav from "./MessageNav.vue";
import MessageCreator from "./MessageCreator.vue";
import MessageList from "./MessageList.vue";
import Profile from "../Profile.vue";
import AudioPlayer from "../MediaViewers/AudioPlayer.vue";
export default {
  components: { MessageList, MessageCreator, Profile, MessageNav, AudioPlayer },
  data: () => {
    return {
      messages: [],
      page: 1,
      totalPages: 1,
      loading: false,
      cancelToken: null,
      source: null,
      Echo: null,
      withFiles: false,
      online: [],
      dialog: false,
      deletableMessage: null,
      typingTimeOut: null,
    };
  },
  destroyed() {
    window.Echo.disconnect();
    window.localStorage.setItem("titleName", "awd");
  },
  mounted() {
    this.connectToPusher();
  },
  watch: {
    chat: function (val) {
      if (val) {
        this.getMessages();
        axios.post(`/api/chats/${val}/mark-as-read`);
      }
      if (val == null) {
        this.page = 1;
        this.totalPages = 1;
      }
    },
  },
  methods: {
    connectToPusher() {
      this.$store
        .dispatch("getUser")
        .then((response) => {
          window.Echo.private(`messages.${response.data.id}`)
            .listen("NewMessage", (e) => {
              this.handleIncoming(e.message);
            })
            .listen("HasRead", (e) => {
              this.marAsRead(e.chat);
            })
            .listen("ChatUpdated", (e) => {
              this.$store.dispatch("updateChat", e.chat);
            })
            .listen("MessageDeleted", (e) => {
              this.handleDeleted(e.message);
            })
            .listen("MessageUpdated", (e) => {
              this.handleUpdated(e.message);
            });
          let channel = window.Echo.join(`chat.1`)
            .here((users) => {})
            .joining((user) => {
              this.$store.dispatch("changeOnlineStatus", {
                status: true,
                id: user.id,
              });
            })
            .leaving((user) => {
              this.$store.dispatch("changeOnlineStatus", {
                status: false,
                id: user.id,
              });
            });
          setTimeout(() => {
            window.Echo.connector.channels["presence-chat.1"].listenForWhisper(
              "typing",
              (e) => {
                this.handleTypingStatus(e);
              }
            );
          }, 0);
        })
        .catch((err) => {});
    },
    handleTypingStatus(e) {
      let sender = e.id;
      if (e.recipient == this.userId)
        this.$store.dispatch("changeTypingStatus", {
          id: sender,
          status: true,
        });
      clearTimeout(this.typingTimeOut);
      this.typingTimeOut = setTimeout(() => {
        this.$store.dispatch("changeTypingStatus", {
          id: sender,
          status: null,
        });
      }, 3100);
    },
    closeBoard(event) {
      this.$store.dispatch("selectChat", this.chat.id);
    },
    showDeleteDialog(message) {
      this.deletableMessage = message;
      this.dialog = true;
    },
    changeWithFiles(value) {
      this.withFiles = value;
    },
    marAsRead(chat) {
      if (this.chat == chat) {
        this.setRead();
      } else {
        this.$store.dispatch("setLastMessageAsRead", chat);
      }
    },
    updateMessage(content, attachment) {
      const config = { "content-type": "multipart/form-data" };
      const formData = new FormData();
      if (content) {
        formData.append("content", content);
      }
      attachment.forEach((file) => {
        formData.append("attachment[]", file);
      });
      axios
        .post(
          "/api/messages/" + this.$store.getters.updateMessage.id,
          formData,
          config
        )
        .then((resp) => {
          this.messages.splice(
            [
              this.messages.indexOf(
                this.messages.find((message) => message.id == resp.data.id)
              ),
            ],
            1,
            resp.data
          );
          this.$store.dispatch("changeUpdateMessage", null);
        });
    },
    deleteMessage() {
      this.dialog = false;
      axios
        .delete("/api/messages/" + this.deletableMessage.id)
        .then((response) => {
          let id = this.messages.indexOf(
            this.messages.find(
              (messagef) => messagef.id == this.deletableMessage.id
            )
          );
          this.messages.splice(id, 1);
          this.deletableMessage = null;
          let lastmessage = this.messages[this.messages.length - 1];
          this.$store.dispatch("setLastMessage", {
            sender: this.chat,
            message: lastmessage,
          });
        });
    },
    handleUpdated(updated_message) {
      if (this.chat == updated_message.sender) {
        this.messages.splice(
          [
            this.messages.indexOf(
              this.messages.find((message) => message.id == updated_message.id)
            ),
          ],
          1,
          updated_message
        );
      }
    },
    handleDeleted(message) {
      if (this.chat == message.sender) {
        let id = this.messages.indexOf(
          this.messages.find((messagef) => messagef.id == message.id)
        );
        this.messages.splice(id, 1);
        let lastmessage = this.messages[this.messages.length - 1];
        this.$store.dispatch("setLastMessage", {
          sender: this.chat,
          message: lastmessage,
        });
      }
    },
    getMessages() {
      this.loading = true;
      if (this.source != null) {
        this.source.cancel();
      }
      this.CancelToken = axios.CancelToken;
      this.source = this.CancelToken.source();
      axios
        .get(`/api/chats/${this.chat}/messages?page=${this.page}`, {
          cancelToken: this.source.token,
        })
        .then((resp) => {
          if (this.page == 1) {
            this.messages = [];
          }
          this.messages = resp.data.data.reverse().concat(this.messages);
          this.totalPages = resp.data.last_page;
          this.page = this.page + 1;
          this.loading = false;
        })
        .catch((err) => {});
    },
    handleIncoming(message) {
      if (this.chat == message.sender) {
        this.messages.push(message);
        this.setRead();
        axios.post(`/api/chats/${message.sender}/mark-as-read`);
        this.setLastMessage(message);
      } else {
        this.$toast.info("New message!", {});
        this.$store.dispatch("checkForChatExists", message).then(() => {
          this.setLastMessage(message);
        });
      }
    },
    setLastMessage(message) {
      this.$store.dispatch("setLastMessage", {
        sender: message.sender,
        message: message,
      });
    },
    makeTempId(length) {
      var result = "";
      var characters =
        "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
      var charactersLength = characters.length;
      for (var i = 0; i < length; i++) {
        result += characters.charAt(
          Math.floor(Math.random() * charactersLength)
        );
      }
      return result;
    },
    send(text, attachment) {
      var tempId = this.makeTempId(5);
      let message = {
        content: text,
        attachments: [],
        created_at: "sending",
        read: 0,
        sender: this.userId,
        recipient: this.chat,
        tempId: tempId,
      };
      this.messages.push(message);
      const config = { "content-type": "multipart/form-data" };
      const formData = new FormData();
      formData.append("content", text);
      formData.append("sender", this.userId);
      formData.append("recipient", this.chat);
      attachment.forEach((file) => {
        formData.append("attachment[]", file);
      });
      axios
        .post("/api/chats/messages", formData, config)
        .then((resp) => {
          let index = this.messages.indexOf(
            this.messages.find((tempMessage) => tempMessage.tempId == tempId)
          );
          this.messages.splice(index, 1, resp.data);
          this.$refs.messageList.scrollDown();
          this.$store.dispatch("setLastMessage", {
            sender: resp.data.recipient,
            message: resp.data,
          });
        })
        .finally(() => {
          this.loading = false;
        });
    },
    stopLoading() {
      this.loading = false;
    },
    setRead() {
      this.messages.forEach((message) => {
        if (message.sender == this.userId) {
          message.read = true;
        }
      });
    },
  },
  computed: {
    chat: function () {
      this.messages = [];
      this.page = 1;
      return this.$store.getters.selectedChat;
    },
    userId: function () {
      return parseInt(localStorage.getItem("userid"));
    },
    selectedProfile: function () {
      return this.$store.getters.selectedProfile;
    },
    selectedAudio: function () {
      return this.$store.getters.selectedAudio;
    },
    theme: function () {
      return this.$store.getters.selectedTheme;
    },
  },
};
</script>

<style lang="scss" scoped>
.board {
  height: 100%;
  width: 75%;
  background: rgb(33, 33, 33);
  position: fixed;
  top: 0;
  right: 0;
  @media (max-width: 424px) {
    width: 100%;
    left: 0 !important;
  }
}
.empty {
  @media (max-width: 424px) {
    display: none !important;
  }
}

.ms {
  margin-left: 10%;
}

.closedProfile {
  width: 75%;
  @media (max-width: 600px) {
    width: 100%;
  }
}
.load {
  position: absolute;
  top: 45%;
  left: 50%;
}
.loadwp {
  left: 35%;
}
@media (max-width: 700px) {
  .board {
    left: 14%;
    right: 25%;
  }
}
.with-filles {
  bottom: 122px;
}
.pam {
  z-index: 9999;
  height: 100%;
  width: 75%;
  width: auto;
}
.audio {
  top: 98px;
  height: auto;
}
</style>
