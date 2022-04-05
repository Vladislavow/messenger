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
          <v-img
            class="img-file"
            v-if="imgExtensions.includes(file.extension)"
            :key="index"
            :src="file.path"
            max-width="300"
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
            v-if="!imgExtensions.includes(file.extension)"
          >
            <v-icon class="file-ico" color="black" large>mdi-file</v-icon>
            <v-icon class="load-ico" color="black" large
              >mdi-file-download</v-icon
            >
            {{ file.original_name }}
          </div>
        </div>
      </div>
      <span parseLinks>{{ message.content }}</span>
      <div class="statuses">
        <span class="time">
          {{ message.created_at | moment("timezone", "Europe/Kiev", "hh:mm") }}
        </span>
        <span class="read">
          <v-icon small v-if="userId != message.recipient">{{
            message.read == true ? "mdi-check-all" : "mdi-check"
          }}</v-icon>
        </span>
      </div>
    </div>
    <v-menu dark v-model="showMenu" :position-x="x" :position-y="y" offset-y>
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
      imgExtensions: ["jpeg", "jpg", "gif", "png", "apng", "svg", "bmp"],
      loading: false,
    };
  },
  props: ["message", "userId"],
  methods: {
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
  @media (max-width:424px) {
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
}
.img-file:hover .img-downolad {
  display: inline-block !important;
}
.img-downolad {
  display: none !important;
  margin: 5px;
}
.img-downolad:hover{
    color: gold !important;
}

.delete {
  color: red;
}
</style>
