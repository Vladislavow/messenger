<template>
  <div
    @scroll="showScrollThumb"
    :class="{ messages: true, scroll: this.scroll_timer != null }"
    ref="container"
    id="mes"
  >
    <!-- <infinite-loading  //to do:inifinite loading
            v-if="messages.length > 0 && page < totalPages + 1"
            @infinite="getMessages"
            direction="top"
            spinner="spiral"
            ref="infin"
            force-use-infinite-wrapper="true"
        /> -->
    <v-btn
      class="loadMore"
      v-if="messages.length > 0 && page < totalPages + 1"
      @click="getMessages"
      :loading="loading"
      rounded
    >
      Load more
    </v-btn>
    <template class="messages" v-for="(message, index) in this.messages">
      <div
        :id="'date' + index"
        :ref="'date' + index"
        v-if="checkDate(message.created_at, index)"
        @click="scrollToBefore(index)"
        class="date"
      >
        {{ dates.get(index) }}
      </div>
      <message
        @deleteMessage="deleteMessage"
        :message="message"
        :userId="userId"
        :key="index"
        @down="scrollDown"
      />
    </template>
  </div>
</template>

<script>
import Message from "./Message.vue";
export default {
  data: () => {
    return {
      scrollLock: false,
      dates: new Map(),
      scroll_timer: null,
    };
  },
  components: { Message },
  props: ["messages", "userId", "loading", "page", "totalPages"],
  watch: {
    messages: function (value) {
      this.scrollDown();
    },
    chat: function (value) {
      this.dates = new Map();
    },
  },
  methods: {
    scrollToBefore(index) {
      this.sortDates();
      let previous = null;
      this.dates.forEach(function (value, key) {
        if (key == index) {
          if (previous || previous == 0) {
            var element = document.getElementById("date" + previous);
            element.scrollIntoView({ behavior: "smooth" });
          }
        } else {
          previous = key;
        }
      });
    },
    scrollDown() {
      if (this.scroll_timer == null)
        setTimeout(() => {
          if (this.scrollLock) {
            this.scrollLock = false;
            return;
          }
          var container = document.getElementById("mes");
          container.scrollTop = container.scrollHeight - container.clientHeight;
        }, 50);
    },
    showScrollThumb() {
      this.scroll_timer = null;
      this.scroll_timer = setTimeout(() => {
        this.scroll_timer = null;
      }, 700);
    },
    deleteMessage(message) {
      this.$emit("deleteMessage", message);
    },
    getMessages() {
      this.scrollLock = true;
      this.$emit("getMessages");
    },
    checkDate(date, index) {
      let current = new Date(date.replace(" ", "T")).toDateString();
      if (index == 0) {
        this.dates.set(index, current);
        return true;
      } else {
        let prev = new Date(
          this.messages[index - 1].created_at.replace(" ", "T")
        ).toDateString();
        if (prev != current) {
          this.dates.set(index, current);
          return true;
        }
      }
      this.checkForUnique();
    },
    sortDates() {
      var temp = new Map(
        [...this.dates.entries()].sort((a, b) => {
          if (a[0] > b[0]) return 1;
          if (a[0] == b[0]) return 0;
          if (a[0] < b[0]) return -1;
        })
      );
      this.dates = temp;
    },
    checkForUnique() {
      let unique = new Set(this.dates.values());
      if (unique.size != this.dates.size) {
        this.dates = new Map();
      }
    },
  },
  mounted() {},
  computed: {
    onBottom: function () {
      var container = document.getElementById("mes");
      return (
        container.scrollHeight - container.clientHeight == container.scrollTop
      );
    },
    chat() {
      return this.$store.getters.selectedChat;
    },
  },
};
</script>

<style lang="scss" scoped>
.messages {
  position: fixed;
  width: 50%;
  height: 77%;
  background: rgba(33, 33, 33, 0);
  overflow: scroll;
  overflow-x: hidden;
  display: flex;
  flex-direction: column;
  top: 58px;
  bottom: 108px;
  @media (min-height: 900px) {
    height: 82%;
  }
  @media (max-width: 424px) {
    width: 100% !important;
    left: 0 !important;
    margin-left: 0px !important;
  }
}
.loadMore {
  margin-top: 3px;
  max-width: 20%;
  box-shadow: none;
  align-self: center;
}
.messages::-webkit-scrollbar {
  width: 5px;
  border-radius: 100px;
}
.messages::-webkit-scrollbar-thumb {
  background-color: white;
  display: none;
  border-radius: 5px;
}
.scroll::-webkit-scrollbar-thumb {
  display: initial;
}
.messages::-webkit-scrollbar-thumb:hover {
  display: initial;
}
.date {
  align-self: center;
  text-align: center;
  color: white;
  background: transparent;
  width: 150px;
  border-radius: 35%;
  border: 2px solid white;
  cursor: pointer;
  margin: 5px 0px;
  background: rgba(255, 255, 255, 0.2);
  border-radius: 16px;
  box-shadow: 0 4px 30px rgba(0, 0, 0, 0.1);
  backdrop-filter: blur(6.3px);
  -webkit-backdrop-filter: blur(6.3px);
  border: 1px solid rgba(255, 255, 255, 0.3);
  -ms-user-select: none;
  -moz-user-select: none;
  -webkit-user-select: none;
  user-select: none;
}
@media (max-width: 700px) {
  .messages {
    width: 61%;
  }
}
</style>
