<template>
  <div :class="{ 'with-files': withFiles, creator: true }">
    <div
      class="files"
      v-if="
        this.attachment.length > 0 ||
        (updateMessage && this.updatingAttachment.length > 0)
      "
    >
      <span v-if="updatingAttachment">
        <v-chip
          close
          @click:close="handleDeleteing(file, index)"
          v-for="(file, index) in updatingAttachment"
          :key="index"
          >{{ getName(file.original_name) }}</v-chip
        >
      </span>
      <v-chip
        close
        @click:close="removeFile(index)"
        v-for="(file, index) in attachment"
        :key="index"
        >{{ getName(file.name) }}</v-chip
      >
    </div>
    <div class="content">
      <twemoji-picker
        :emojiData="emojiDataAll"
        :emojiGroups="emojiGroups"
        :skinsSelection="false"
        :searchEmojisFeat="true"
        searchEmojiPlaceholder="Search here."
        searchEmojiNotFound="Emojis not found."
        isLoadingLabel="Loading..."
        pickerCloseOnClickaway
        @emojiUnicodeAdded="emojiUnicodeAdded"
        :emojiPickerDisabled="loading"
      ></twemoji-picker>
      <v-textarea
        class="text"
        no-resize
        filled
        placeholder="Message here"
        v-model="content"
        clearable
        rows="2"
        rounded
        dark
        @keypress.enter="keysHandling"
      >
        <template v-if="updateMessage != null" v-slot:prepend>
          <v-icon @click="closeUpdate"> mdi-pencil-remove </v-icon>
        </template>
        <template v-slot:append>
          <v-icon
            :disabled="
              attachment.length +
                (updatingAttachment ? updatingAttachment.length : 0) >
              5
            "
            @click="$refs.file.click()"
          >
            mdi-paperclip
          </v-icon>
        </template></v-textarea
      >
      <input type="file" hidden ref="file" @change="addFile" />
      <v-btn :loading="this.loading" fab dark @click="send">
        <v-icon color="white">{{
          updateMessage != null ? "mdi-pencil" : "mdi-send"
        }}</v-icon>
      </v-btn>
    </div>
    <v-dialog
      transition="dialog-bottom-transition"
      v-model="dialog"
      persistent
      max-width="290"
    >
      <v-card>
        <v-card-title class="text-h5"> Delete attachment? </v-card-title>

        <v-card-text> You will not be able to undo this action </v-card-text>

        <v-card-actions>
          <v-spacer></v-spacer>

          <v-btn color="green darken-1" text @click="dialog = false">
            Close
          </v-btn>

          <v-btn color="green darken-1" text @click="deleteFile">
            Delete
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </div>
</template>

<script>
import { TwemojiPicker } from "@kevinfaguiar/vue-twemoji-picker";
import EmojiAllData from "@kevinfaguiar/vue-twemoji-picker/emoji-data/en/emoji-all-groups.json";
import EmojiDataAnimalsNature from "@kevinfaguiar/vue-twemoji-picker/emoji-data/en/emoji-group-animals-nature.json";
import EmojiDataFoodDrink from "@kevinfaguiar/vue-twemoji-picker/emoji-data/en/emoji-group-food-drink.json";
import EmojiGroups from "@kevinfaguiar/vue-twemoji-picker/emoji-data/emoji-groups.json";
export default {
  components: { TwemojiPicker },
  props: ["loading", "withFiles"],
  data: () => {
    return {
      content: "",
      attachment: [],
      updating: {},
      updatingAttachment: [],
      dialog: false,
      deleteingFile: {
        file: null,
        index: null,
      },
    };
  },
  watch: {
    updateMessage: function (value) {
      if (value) {
        this.content = this.updateMessage.content;
        this.attachment = [];
        this.updatingAttachment = this.updateMessage.attachments;
      }
    },
  },
  methods: {
    closeUpdate() {
      this.$store.dispatch("changeUpdateMessage", null);
      this.content = "";
    },
    send() {
      if (true) {
        if (this.updateMessage) {
          this.$emit("updateMessage", this.content, this.attachment);
          this.content = "";
          this.attachment = [];
          this.updatingAttachment = [];
        } else {
          this.$emit("send", this.content, this.attachment);
          this.attachment = [];
          this.content = "";
          this.$emit("changeWithFiles", false);
        }
      }
    },
    keysHandling(event) {
      if (event.shiftKey === true && event.key === "Enter") {
        return;
      }
      event.preventDefault();
      this.send();
      return false;
    },
    addFile(e) {
      if (e.target.files.length > 0) {
        if (e.target.files[0].size < 10485760) {
          this.attachment.push(e.target.files[0]);
          this.$emit("changeWithFiles", true);
        } else {
          this.$toast.error("Maximum size 10mb!");
        }
      }
    },
    getName(name) {
      let fname = name.split(".")[0];
      let ext = name.split(".")[1];
      if (fname.length > 10) {
        fname = fname.substring(0, 7) + "..." + fname.substring(7, 10);
      }
      return fname + "." + ext;
    },
    handleDeleteing(file, index) {
      this.dialog = true;
      this.deleteingFile = { file: file, index: index };
    },
    removeFile(index) {
      this.attachment.splice(index, 1);
      if (this.attachment.length == 0) {
        this.$emit("changeWithFiles", false);
      }
    },
    deleteFile() {
      axios
        .delete("/api/attachment/" + this.deleteingFile.file.id)
        .then((response) => {
          this.updatingAttachment.splice(this.deleteingFile.index, 1);
        })
        .finally(() => {
          this.deleteingFile = null;
          this.dialog = false;
        });
    },
    emojiUnicodeAdded(emojiUnicode) {
      this.content += emojiUnicode;
    },
  },
  computed: {
    emojiDataAll() {
      return EmojiAllData;
    },
    emojiGroups() {
      return EmojiGroups;
    },
    updateMessage: function () {
      return this.$store.getters.updateMessage;
    },
  },
};
</script>

<style lang="scss" scoped>
.creator {
  position: fixed;
  width: 50%;
  height: 15%;
  bottom: 0;
  display: inline;
  background: rgba(33, 33, 33, 0);
  padding: 10px;
  @media (min-height: 900px) {
    height: 12% !important;
  }
  @media (max-width: 424px) {
    width: 100% !important;
    left: 0 !important;
    margin-left: 0px !important;
  }
}
.content {
  display: flex;
}
.files {
  display: flex;
  overflow: hidden;
  margin-bottom: 3px;
}

.files::-webkit-scrollbar {
  width: 5px;
  border-radius: 100px;
}
.files::-webkit-scrollbar-thumb {
  background-color: white;
  display: none;
  border-radius: 5px;
}
.files::-webkit-scrollbar-thumb:hover {
  display: initial;
}
.v-btn {
  margin-left: 3px;
}
@media (max-width: 700px) {
  .creator {
    width: 61%;
  }
}
.with-files {
  height: 18%;
  @media (min-height: 900px) {
    height: 13% !important;
  }
}

.text {
  overflow: hidden !important;
}

textarea * {
  display: none !important;
  overflow: hidden !important;
}

.text::-webkit-scrollbar-thumb {
  background-color: white;
  display: none;
  border-radius: 5px;
}
</style>
