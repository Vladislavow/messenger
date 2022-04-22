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
      <div class="statuses">
        <div v-if="!chat.typing" class="online">
          {{
            chat.online
              ? "Online"
              : chat.last_seen
              ? "Last seen: " + c_last_seen
              : "Offline"
          }}
        </div>
        <div v-if="chat.typing" class="typing"></div>
      </div>
    </div>
  </div>
</template>

<script>
var DateDiff = {
  inMinutes: function (d1, d2) {
    var t2 = d2.getTime();
    var t1 = d1.getTime();

    return Math.floor((t2 - t1) / (60 * 1000));
  },
  inHours: function (d1, d2) {
    var t2 = d2.getTime();
    var t1 = d1.getTime();

    return Math.floor((t2 - t1) / (3600 * 1000));
  },
  inDays: function (d1, d2) {
    var t2 = d2.getTime();
    var t1 = d1.getTime();

    return Math.floor((t2 - t1) / (24 * 3600 * 1000));
  },
  inWeeks: function (d1, d2) {
    var t2 = d2.getTime();
    var t1 = d1.getTime();

    return parseInt((t2 - t1) / (24 * 3600 * 1000 * 7));
  },

  inMonths: function (d1, d2) {
    var d1Y = d1.getFullYear();
    var d2Y = d2.getFullYear();
    var d1M = d1.getMonth();
    var d2M = d2.getMonth();

    return d2M + 12 * d2Y - (d1M + 12 * d1Y);
  },

  inYears: function (d1, d2) {
    return d2.getFullYear() - d1.getFullYear();
  },
};
export default {
  data: () => {
    return {
      dateArr: { seconds: 15 * 1000, minute: 60 * 1000, hour: 3600 * 1000 },
      now: new Date(),
    };
  },
  methods: {
    timeUpdater() {
      this.now = new Date();
      let diff = this.now - new Date(this.chat.last_seen);
      if (this.chat) {
        let last_seen = new Date(this.chat.last_seen);
        if (
          DateDiff.inMinutes(last_seen, this.now) > 1 &&
          DateDiff.inHours(last_seen, this.now) <= 0
        ) {
          setTimeout(() => this.timeUpdater(), 60000 - (diff % (60 * 1000)));
        } else if (DateDiff.inHours(last_seen, this.now) > 0) {
          setTimeout(
            () => this.timeUpdater(),
            3600 * 1000 - (diff % (3600 * 1000))
          );
        } else if (DateDiff.inMinutes(last_seen, this.now) < 1) {
          setTimeout(() => this.timeUpdater(), 60 * 1000 - diff);
        }
      } else {
        setTimeout(() => this.timeUpdater(), 15 * 1000);
      }
    },
    showProfile() {
      if (this.chat) {
        this.$store.dispatch("setSelectedProfile", this.chat.id);
      }
    },
    closeBoard() {
      this.$store.dispatch("selectChat", this.chat.id);
    },
  },
  mounted() {
    this.timeUpdater();
  },
  computed: {
    chat: function () {
      return this.$store.getters.selectedChatInfo;
    },
    c_last_seen: function () {
      let now = this.now;
      if (this.chat) {
        let last_seen = new Date(this.chat.last_seen.replace(" ", "T"));
        let diff = now - last_seen;
        if (diff < 60 * 1000) {
          return "just now";
        } else if (diff < 60 * 60 * 1000) {
          const temp = DateDiff.inMinutes(last_seen, now);
          return `${temp} minute${temp == 1 ? "" : "s"} ago`;
        } else if (diff < 24 * 60 * 60 * 1000) {
          const temp = DateDiff.inHours(last_seen, now);
          return `${temp} hour${temp == 1 ? "" : "s"} ago`;
        } else {
          return last_seen.toDateString();
        }
      }
    },
  },
};
</script>

<style lang="scss" scoped>
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
  @media (max-width: 424px) {
    width: 100%;
  }
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

.statuses {
  display: flex;
  font-size: 12px;
  margin-bottom: 3px;
}
.close-btn {
  align-self: center;
  display: none;
  @media (max-width: 424px) {
    display: initial;
  }
}
.typing {
  content: "";
  position: relative;
}
.typing::after {
  content: "";
  position: absolute;
  top: 0;
  left: 0;
  animation: typing 0.7s infinite;
}
@keyframes typing {
  0% {
    content: "Typing";
  }
  25% {
    content: "Typing.";
  }
  50% {
    content: "Typing..";
  }
  100% {
    content: "Typing...";
  }
}
</style>
