<template>
    <div>
        <chat-list />
    <message-board />     
    </div>
</template>

<script>
import MessageBoard from './MessageBoard.vue';
import ChatList from './ChatList.vue';
export default {
  components: { MessageBoard, ChatList },};
</script>

<style scoped>
</style>
