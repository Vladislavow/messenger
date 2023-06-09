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
            @click:close="handleDeleting(file, index)"
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
            >{{ getName(file.name) }}
            </v-chip
            >
        </div>
        <div class="content">
            <v-btn
                class="emoji-switch"
                plain
                fab
                dark
                small
                @click="showDialog=!showDialog"
            >
                <v-icon>mdi-emoticon</v-icon>
            </v-btn>
            <VEmojiPicker
                v-show="showDialog"
                :style="{ bottom: '94px', left: '-100px', position: 'absolute' }"
                labelSearch="Search"
                lang="pt-BR"
                @select="emojiUnicodeAdded"
            />
            <v-textarea
                :class="{ text: true, drag: drag }"
                no-resize
                filled
                :placeholder="drag ? 'Drop here' : 'Message here'"
                v-model="content"
                clearable
                rows="2"
                :rounded="!drag"
                dark
                @drop="drop"
                @dragenter="drag = true"
                @dragleave="drag = false"
                @keypress.enter="keysHandling"
                hide-details
                @input="sendTypingStatus"
            >
                <template v-if="updateMessage != null" v-slot:prepend>
                    <v-icon @click="closeUpdate"> mdi-pencil-remove</v-icon>
                </template>
                <template class="paperclip" v-slot:append>
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
                </template>
            </v-textarea
            >
            <input type="file" hidden ref="file" @change="addFile"/>
            <v-btn
                :disabled="!content && attachment.length == 0"
                :loading="this.loading"
                fab
                dark
                @click="send"
            >
                <v-icon color="white">{{
                        updateMessage != null ? "mdi-pencil" : "mdi-send"
                    }}
                </v-icon>
            </v-btn>
        </div>
        <v-dialog
            transition="dialog-bottom-transition"
            v-model="dialog"
            persistent
            max-width="290"
        >
            <v-card>
                <v-card-title class="text-h5"> Delete attachment?</v-card-title>

                <v-card-text> You will not be able to undo this action</v-card-text>

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
import {VEmojiPicker} from "v-emoji-picker";

export default {
    components: {VEmojiPicker},
    props: ["loading", "withFiles"],
    data: () => {
        return {
            content: "",
            attachment: [],
            updating: {},
            updatingAttachment: [],
            dialog: false,
            deletingFile: {
                file: null,
                index: null,
            },
            drag: false,
            typing: false,
            showDialog: false
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
        sendTypingStatus() {
            if (this.typing === false) {
                window.Echo.connector.channels["presence-chat.1"].whisper("typing", {
                    recipient: this.chat,
                    id: this.userId,
                    typing: true,
                });
                this.typing = true;
                setTimeout(() => {
                    this.typing = false;
                }, 3000);
            }
        },
        drop(e) {
            e.preventDefault();
            var text = e.dataTransfer.getData("Text");
            if (text) {
                this.content += text;
            }
            if (e.dataTransfer.files.length >= 1) {
                [...e.dataTransfer.files].forEach((file) => {
                    if (file.size < 10485760) {
                        this.attachment.push(file);
                    } else {
                        this.$toast.error(e.target.name + " bigger than 10 mb");
                    }
                });
            }
            this.drag = false;
        },
        closeUpdate() {
            this.$store.dispatch("changeUpdateMessage", null);
            this.content = "";
        },
        send() {
            if (this.updateMessage) {
                if (
                    this.content ||
                    this.attachment.length > 0 ||
                    this.updatingAttachment.length > 0
                ) {
                    this.$emit("updateMessage", this.content, this.attachment);
                    this.content = "";
                    this.attachment = [];
                    this.updatingAttachment = [];
                } else {
                    this.$toast.error("Message empty");
                }
            } else {
                if (this.content || this.attachment.length > 0) {
                    this.$emit("send", this.content, this.attachment);
                    this.attachment = [];
                    this.content = "";
                    this.$emit("changeWithFiles", false);
                } else {
                    this.$toast.error("Message empty");
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
        handleDeleting(file, index) {
            this.dialog = true;
            this.deletingFile = {file: file, index: index};
        },
        removeFile(index) {
            this.attachment.splice(index, 1);
            if (this.attachment.length == 0) {
                this.$emit("changeWithFiles", false);
            }
        },
        deleteFile() {
            axios
                .delete("/api/attachments/" + this.deletingFile.file.id)
                .then((response) => {
                    this.updatingAttachment.splice(this.deletingFile.index, 1);
                })
                .finally(() => {
                    this.deletingFile = null;
                    this.dialog = false;
                });
        },
        emojiUnicodeAdded(emojiUnicode) {
            this.content += emojiUnicode.data;
        },
    },
    computed: {
        updateMessage: function () {
            return this.$store.getters.updateMessage;
        },
        chat: function () {
            return this.$store.getters.selectedChat;
        },
        userId: function () {
            return parseInt(localStorage.getItem("userid"));
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
    transition: 0.5s;
    @media (min-height: 900px) {
        height: 12% !important;
    }
    @media (max-width: 424px) {
        width: 100% !important;
        left: 0 !important;
        margin-left: 0 !important;
    }
}

@media (max-width: 900px) {
    .emoji-switch {
        display: none;
    }
}


.content {
    display: flex;
    align-items: center;
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

.drag {
    // border: 1px dashed white;

    background-image: linear-gradient(110deg, silver 50%, transparent 50%),
    linear-gradient(90deg, silver 50%, transparent 50%),
    linear-gradient(0deg, silver 50%, transparent 50%),
    linear-gradient(0deg, silver 50%, transparent 50%);
    background-repeat: repeat-x, repeat-x, repeat-y, repeat-y;
    background-size: 15px 2px, 15px 2px, 2px 15px, 2px 15px;
    background-position: left top, right bottom, left bottom, right top;
    animation: border-dance 0.5s infinite linear;
    // border-radius: 10%;
}

@keyframes border-dance {
    0% {
        background-position: left top, right bottom, left bottom, right top;
    }
    100% {
        background-position: left 15px top, right 15px bottom, left bottom 15px,
        right top 15px;
    }
}

.mdi-paperclip:hover {
    transform: scale(1.5);
}
</style>
