<template>
  <div class="viewer" @click="close">
    <v-img
      @click.stop
      class="image"
      contain
      aspect-ratio="2.3"
      :src="image.path"
      ><template v-slot:placeholder>
        <v-row class="fill-height ma-0" align="center" justify="center">
          <v-progress-circular
            indeterminate
            color="grey lighten-5"
          ></v-progress-circular>
        </v-row> </template
    ></v-img>
    <div class="btns">
      <v-btn :loading="loading" small plain @click.stop="downloadImg" fab
        ><v-icon color="green">mdi-download</v-icon></v-btn
      >
      <v-btn small plain @click.stop="deleteImg" fab
        ><v-icon color="red">mdi-delete</v-icon></v-btn
      >
      <v-btn small plain @click="close" fab
        ><v-icon color="white">mdi-close</v-icon></v-btn
      >
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      loading: false,
    };
  },
  computed: {
    image: function () {
      return this.$store.getters.selectedImage;
    },
  },
  methods: {
    close() {
      this.$store.dispatch("changeSelectedImage", null);
    },
    downloadImg() {
      this.loading = true;
      axios
        .get("/api/download/" + this.image.id, {
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
    deleteImg() {
      axios
        .delete("/api/attachment/" + this.image.id)
        .then((response) => {
          this.$toast.success("Image deleted");
          this.close();
        })
        .finally(() => {});
    },
  },
};
</script>

<style lang="scss" scoped>
.viewer {
  position: absolute;
  right: 0;
  left: 0;
  top: 0;
  left: 0;
  background: rgba(33, 33, 33, 0.9);
  padding: 5%;
  width: 100%;
  height: 100%;
}

.btns {
  position: absolute;
  right: 15px;
  top: 15px;
  display: flex;
  align-items: center;
}
</style>