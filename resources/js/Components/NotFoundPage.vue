<template>
  <div class="back">
    <marquee behavior="alternate" scrollamount="30">
      <marquee behavior="alternate" scrollamount="30" direction="down">
        <div class="not-found-container">
          <span class="code">404</span>
          <p class="not-found">Not Found!</p>
        </div>
      </marquee>
    </marquee>
    <router-link class="home-btn" to="/">
      <v-btn>Go home</v-btn>
    </router-link>
  </div>
</template>

<script>
export default {};
</script>

<style lang="scss" scoped>
.back {
  height: 100%;
  width: 100%;
  background: rgb(33, 33, 33);
  display: flex;
  justify-content: center;
}
.not-found-container {
  color: white;
  z-index: 100;
}
.code {
  font-size: 70px;
  margin-left: 15px;
  border-bottom: 2px solid white;
}
.not-found {
  font-size: 30px;
}
marquee {
  height: 100vh;
}
.home-btn {
  position: absolute;
  align-self: center;
  justify-self: center;
  text-align: center;
}
</style>