<template>
  <div
    :class="{
      received: userId == message.recipient,
      message,
    }"
    @contextmenu.prevent="show"
  >
    <div class="content">
      <div v-if="message.attachments.length > 0">
        <div v-for="(file, index) in message.attachments" :key="index">
          <div class="player" v-if="audioExtensions.includes(file.extension)">
            <v-btn dark depressed fab x-small @click="selectAudio(file)">
              <v-icon>mdi-play</v-icon>
            </v-btn>
            <div class="audio_title">{{ file.original_name }}</div>
          </div>
          <v-img
            :id="'attachment.' + file.id"
            @click="selectImage(file)"
            class="img-file"
            v-if="imgExtensions.includes(file.extension)"
            :key="index"
            :src="file.path"
            max-width="300"
            lazy-src="storage/placeholder.png"
          >
            <template>
              <v-icon
                v-if="!loading"
                @click.prevent="download(file)"
                cmall
                class="img-downolad"
                color="white"
              >
                mdi-download</v-icon
              >
              <v-progress-circular
                v-if="loading"
                indeterminate
                color="white"
              ></v-progress-circular>
            </template>
            <template v-slot:placeholder>
              <v-row class="fill-height ma-0" align="center" justify="center">
                <v-progress-circular
                  indeterminate
                  color="grey lighten-5"
                ></v-progress-circular>
              </v-row> </template
          ></v-img>
          <div
            class="file"
            @click="download(file)"
            v-if="
              !imgExtensions.includes(file.extension) &&
              !audioExtensions.includes(file.extension)
            "
          >
            <v-icon class="file-ico" color="black" large>mdi-file</v-icon>
            <v-icon class="load-ico" color="black" large
              >mdi-file-download</v-icon
            >
            {{ file.original_name }}
          </div>
        </div>
      </div>
      <span class="text" v-if="hasLink()">
        <span v-for="(item, index) in contentArr" :key="index">
          {{ !item.is_link ? item.content : "" }}
          <a v-if="item.is_link" target="_blank" :href="item.content">{{
            item.content
          }}</a>
        </span>
      </span>
      <span v-if="!hasLink()" class="text">
        {{ message.content }}
      </span>
      <div class="statuses">
        <span
          v-if="message.created_at && message.created_at == 'sending'"
          class="time"
        >
          <v-icon small>mdi-clock-outline</v-icon>
        </span>
        <span
          v-if="message.created_at && message.created_at != 'sending'"
          class="time"
        >
          {{ message.created_at | moment("timezone", "Europe/Kiev", "hh:mm") }}
        </span>
        <span class="read">
          <v-icon small v-if="userId != message.recipient">{{
            message.read == true ? "mdi-check-all" : "mdi-check"
          }}</v-icon>
        </span>
      </div>
    </div>
    <v-menu
      v-if="message.created_at && message.created_at != 'sending'"
      dark
      v-model="showMenu"
      :position-x="x"
      :position-y="y"
      offset-y
    >
      <v-list>
        <div @mouseleave="hide">
          <v-list-item @click="updateMessage"
            ><v-icon>mdi-pencil</v-icon> Update
          </v-list-item>
          <v-list-item color="purple" class="delete" @click="deleteMessage"
            ><v-icon color="red">mdi-delete</v-icon> Delete
          </v-list-item>
        </div>
      </v-list>
    </v-menu>
  </div>
</template>

<script>
export default {
  data: () => {
    return {
      showMenu: false,
      x: 0,
      y: 0,
      loading: false,
    };
  },
  props: ["message", "userId", "imgExtensions", "audioExtensions"],
  mounted() {
  },
  methods: {
    hasLink() {
      var expression =
        /https?:\/\/(www\.)?[-a-zA-Z0-9@:%._\+~#=]{1,256}\.[a-zA-Z0-9()]{1,6}\b([-a-zA-Z0-9()@:%_\+.~#?&//=]*)/gi;
      var regex = new RegExp(expression);
      if (this.message.content) {
        return this.message.content.match(expression) != null;
      } else {
        return false;
      }
    },
    handleLinks() {
      let arr = [];
      var expression =
        /(https?:\/\/)?[\w\-~]+(\.[\w\-~]+)+(\/[\w\-~@:%]*)*(#[\w\-]*)?(\?[^\s]*)?/gi;
      var regex = new RegExp(expression);
      if (this.message.content) {
        let links = this.message.content.match(expression);
        if (links && links.length > 0) {
          links.forEach((link) => {
            if (arr.length == 0) {
              let temp = this.message.content.split(link);
              arr.push({
                content: temp[0],
                is_link: false,
              });
              arr.push({
                content: link,
                is_link: true,
              });
              arr.push({
                content: temp[1],
                is_link: false,
              });
            } else {
              let temp = arr[arr.length - 1].content.split(link);
              arr.splice(arr.length - 1, 1);
              arr.push({
                content: temp[0],
                is_link: false,
              });
              arr.push({
                content: link,
                is_link: true,
              });
              arr.push({
                content: temp[1],
                is_link: false,
              });
            }
          });
        }

        return arr;
      }
      return this.message.content;
    },
    scrollDown(e) {
      this.$emit("down");
    },
    show(e) {
      e.preventDefault();
      if (this.message.sender == localStorage.getItem("userid")) {
        this.showMenu = false;
        this.x = e.clientX;
        this.y = e.clientY;
        this.$nextTick(() => {
          this.showMenu = true;
        });
      }
    },
    hide() {
      this.showMenu = false;
    },
    updateMessage() {
      this.$store.dispatch("changeUpdateMessage", this.message);
    },
    deleteMessage() {
      this.$emit("deleteMessage", this.message);
    },
    selectAudio(file) {
      this.$store.dispatch("changeSelectedAudio", file);
    },
    selectImage(file) {
      this.$store.dispatch("changeSelectedImage", file);
    },
    download(file) {
      this.loading = true;
      axios
        .get("/api/download/" + file.id, {
          responseType: "blob",
        })
        .then((response) => {
          var fileURL = window.URL.createObjectURL(new Blob([response.data]));
          var fileLink = document.createElement("a");
          fileLink.href = fileURL;
          fileLink.setAttribute("download", `${file.original_name}`);
          document.body.appendChild(fileLink);
          fileLink.click();
        })
        .catch((error) => {
          this.$toast.error(error.response.data.message);
        })
        .finally(() => {
          this.loading = false;
        });
    },
    // getFile(file) { //to do: customize image src
    //   axios
    //     .get("/api/attachment/" + file, { responseType: "blob" })
    //     .then((response) => {
    //       let url = URL.createObjectURL(response.data);
    //       let fileContainer = document.getElementById("attachment." + file);
    //       console.log(url);
    //       console.log(fileContainer);
    //       // fileContainer.setAttribute("src", url);
    //       console.log(fileContainer);
    //       return url;
    //     });
    // },
  },
  computed: {
    contentArr: function () {
      return this.handleLinks();
    },
  },
};
</script>

<style lang="scss" scoped>
.message {
  display: inline-block;
  position: relative;
  /* left: 80%; */
  background: rgb(6, 153, 13);
  max-width: 70%;
  border-radius: 15px;
  padding: 5px;
  margin: 10px;
  vertical-align: right;
  align-self: flex-end;
}
.content {
  word-wrap: break-word;
  margin-left: 5px;
  margin-right: 5px;
  font-size: 22px;
  @media (max-width: 424px) {
    font-size: 17px !important;
  }
}
.received {
  align-self: flex-start;
  text-align: left;
  left: 0;
  background: rgb(255, 255, 255);
  &:after {
    border-bottom-color: white !important;
    left: 20px;
  }
}
.text {
  word-break: break-all;
}

.time {
  position: inline;
  justify-self: right;
  font-size: 14px;
  -ms-user-select: none;
  -moz-user-select: none;
  -webkit-user-select: none;
  user-select: none;
  @media (max-width: 424px) {
    font-size: 11px !important;
  }
}

.message::after {
  bottom: 100%;
  right: 20px;
  border: solid transparent;
  content: " ";
  height: 0;
  width: 0;
  position: absolute;
  pointer-events: none;
  border-bottom-color: rgb(6, 153, 13);
  border-width: 10px;
  @media (max-width: 424px) {
    bottom: 96% !important;
  }
}

.statuses {
  position: relative;
  bottom: -3px;
  float: right;
  margin-left: 3px;
  display: inline;
  -ms-user-select: none;
  -moz-user-select: none;
  -webkit-user-select: none;
  user-select: none;
  @media (max-width: 424px) {
    .read * {
      font-size: 13px !important;
    }
  }
}

.file {
  color: black;
  padding: 5px;
  font-size: 18px;
}
.file:hover .load-ico {
  display: inline-flex !important;
}
.file:hover .file-ico {
  display: none !important;
}
.load-ico {
  display: none !important;
}
.img-file {
  border-radius: 15px;
  margin-top: 5px;
  background: white;
}
.img-file:hover .img-downolad {
  display: inline-block !important;
}
.img-downolad {
  display: none !important;
  margin: 5px;
}
.img-downolad:hover {
  color: gold !important;
}

.delete {
  color: red;
}
.player {
  display: flex;
  align-items: center;
}
.audio_title {
  margin-left: 10px;
}
</style>
