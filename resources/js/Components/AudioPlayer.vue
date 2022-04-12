<template>
  <div class="player">
    <div class="controllers">
      <v-btn fab plain small color="transparent"
        ><v-icon color="white" @click="previous"
          >mdi-skip-previous</v-icon
        ></v-btn
      >
      <v-btn fab plain small @click="play(false)" color="transparent"
        ><v-icon color="white">{{
          playing ? "mdi-pause" : "mdi-play"
        }}</v-icon></v-btn
      >
      <v-btn fab plain small color="transparent"
        ><v-icon color="white" @click="next">mdi-skip-next</v-icon></v-btn
      >
    </div>
    <div class="track">
      <v-slider
        :value="progress"
        :max="duration"
        hide-details
        @change="setProgress"
      ></v-slider>
    </div>

    <div class="time">
      {{ parseTime(progress) + "/" + parseTime(duration) }}
    </div>
    <v-btn class="close-btn" fab plain small color="transparent"
      ><v-icon color="white" @click="close">mdi-close</v-icon></v-btn
    >
    <audio
      ref="player"
      @timeupdate="changeProgress"
      @durationchange="changeDuration"
      @canplay="play(true)"
      @ended="playing = false"
      type="audio/ogg"
    ></audio>
  </div>
</template>

<script>
export default {
  data() {
    return {
      player: null,
      playing: false,
      progress: 0,
      duration: 0,
    };
  },
  watch:{
    audio: function(value){
      if(value.path!=null){
          this.getFile(value.id);
      }
    }
  },
  mounted() {
    let player = this.$refs.player;
    if (player) {
      this.player = player;
      this.playing = !this.player.paused;
      if(this.audio){
        this.getFile(this.audio.id)
      }
    }
  },
  methods: {
    previous() {
      if (this.progress > 0) {
        this.player.currentTime = 0;
      }
    },
    play(force) {
      if (force) {
        this.player.play();
      } else {
        if (!this.player.paused) {
          this.player.pause();
        } else {
          this.audio.src = 'blob:http://127.0.0.1:8000/c2b743c0-0f21-4127-8429-c9577b019462';
          this.player.load();
          this.player.play();
        }
      }
      this.playing = !this.player.paused;
    },
    setProgress(value) {
      this.player.pause();
      this.player.currentTime = value;
      this.player.play();
    },
    next() {},
    close() {
      this.$store.dispatch("changeSelectedAudio", null);
    },
    changeProgress(e) {
      this.progress = parseInt(this.player?.currentTime, 10);
    },
    changeDuration(e) {
      this.duration = parseInt(this.player?.duration, 10);
    },
    getFile(value) {
      axios
        .get("/api/song/" + value, { responseType: "blob" })
        .then((response) => {
          let url = URL.createObjectURL(response.data)
          this.player.src = url;
        });
    },
    parseTime(value) {
      return (
        Math.trunc(value / 60) +
        ":" +
        String((value / 60).toFixed(2)).split(".")[1]
      );
    },
  },
  computed: {
    audio: function(){
      return this.$store.getters.selectedAudio;
    }
  },
};
</script>

<style lang="scss" scoped>
.player {
  width: 50%;
  height: 40px;
  background: rgba(33, 33, 33, 0.9);
  position: fixed;
  top: 58px;
  display: flex;
}
.controllers {
  margin-left: 15px;
  display: flex;
}
.track {
  width: 83%;
  align-items: center;
  padding: 2px;
}
.close-btn {
  align-self: flex-end;
}
.time {
  color: white;
  align-self: center;
}
</style>