<template>
  <div class="settings">
    <div class="s-title">
      <v-icon class="cog-icon" large color="white">mdi-cog-outline</v-icon
      >Settings
    </div>
    <div class="sets-items">
      <div>
        <v-icon color="white">mdi-panorama-variant-outline</v-icon>
        Theme:
        <v-select
          @change="setTheme"
          v-model="selectedTheme"
          :items="themes"
          dark
        />
      </div>
      <div class="sessions">
        <v-icon color="white">mdi-cellphone-link</v-icon> Current sessions:
        <v-btn @click="getActiveSessions" dark fab small plain :loading="loading"
          ><v-icon color="white">mdi-reload</v-icon></v-btn
        >
        <div
          :class="{ session: true, current: isCurrent(session.id) }"
          v-for="(session, index) in sessions"
          :key="index"
        >
          {{ session.name + " " + new Date(session.updated_at).toDateString() }}
          <v-btn
            fab
            small
            plain
            @click="showCloseDialog(session.id)"
            v-if="!isCurrent(session.id)"
            class="exit-icon"
            ><v-icon color="red">mdi-logout</v-icon></v-btn
          >
        </div>
      </div>
    </div>
    <v-dialog
      transition="dialog-bottom-transition"
      v-model="dialog"
      persistent
      max-width="290"
    >
      <v-card>
        <v-card-title class="text-h5"> Close session? </v-card-title>

        <v-card-text> You will not be able to undo this action </v-card-text>

        <v-card-actions>
          <v-spacer></v-spacer>

          <v-btn color="green darken-1" text @click="dialog = false">
            Close
          </v-btn>

          <v-btn
            color="red darken-1"
            text
            @click="closeSession(selectedForClose)"
          >
            Close session
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </div>
</template>

<script>
export default {
  data: () => {
    return {
      selectedTheme: null,
      themes: [
        { text: "Animated shapes", value: "animated" },
        { text: "Colored shapes", value: "colored" },
        { text: "World map", value: "map" },
        { text: "Icons", value: "icons" },
        { text: "Cloudly", value: "clouds" },
        { text: "Lines", value: "line" },
        { text: "Polygon", value: "polygon" },
        { text: "Shaped", value: "shaped" },
        { text: "Sound", value: "sound" },
        { text: "Wawes", value: "wawe" },
      ],
      sessions: [],
      currentSessionId: null,
      dialog: false,
      selectedForClose: null,
      loading: false,
    };
  },
  watch: {
    theme: function (value) {
      this.selectedTheme = value;
    },
  },
  mounted() {
    this.selectedTheme = this.theme;
    this.getActiveSessions();
    this.getCurrentSession();
  },
  methods: {
    showCloseDialog(id) {
      this.selectedForClose = id;
      this.dialog = true;
    },
    closeSession() {
      axios
        .delete("/api/session/" + this.selectedForClose)
        .then((resp) => {
          this.$toast.success(resp.data);
          this.getActiveSessions();
        })
        .catch((error) => {
          this.$toast.error("Something went wrong");
        })
        .finally(() => {
          this.dialog = false;
        });
    },
    getActiveSessions() {
      this.sessions = null;
      this.loading = true;
      axios.get("/api/sessions").then((resp) => {
        this.sessions = resp.data;
        this.loading = false;
      });
    },
    getCurrentSession() {
      axios.get("/api/current_token_id").then((resp) => {
        this.currentSessionId = resp.data;
      });
    },
    setTheme(value) {
      this.$store.dispatch("changeSelectedTheme", value);
    },
    isCurrent(id) {
      return id == this.currentSessionId;
    },
  },
  computed: {
    theme: function () {
      return this.$store.getters.selectedTheme;
    },
  },
};
</script>

<style lang="scss" scoped>
.s-title {
  display: flex;
  justify-content: left;
  align-items: center;
  color: white;
  font-size: 28px;
  margin-left: 2px;
  padding: 5px 7px;
  .cog-icon {
    margin-right: 5px;
  }
}
.sets-items * {
  color: white;
  font-size: 22px;
  word-break: break-all;
  padding: 5px 7px;
  border-radius: 10px;
  margin: 2px;
  align-items: center;
}
.sessions {
  .session {
    border-radius: 15px;
    display: flex;
    justify-content: space-between;
  }
}
.session:hover {
  background: rgb(77, 73, 73);
}
.exit-icon {
  right: 15px;
}
.current {
  background: rgb(33, 150, 33);
}
</style>