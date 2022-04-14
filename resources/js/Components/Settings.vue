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
    </div>
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
    };
  },
  watch: {
    theme:function(value) {
        this.selectedTheme = value
    },
  },
  mounted(){
       this.selectedTheme = this.theme
  },
  methods: {
    setTheme(value) {
      this.$store.dispatch('changeSelectedTheme', value)
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
</style>