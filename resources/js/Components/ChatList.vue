<template>
    <div class="chats">
        <div v-for="(chat, index) in this.chats" :key="index" >
            <chat-item :chat="chat" />
        </div>
    </div>
</template>

<script>
import ChatItem from './ChatItem.vue'
export default {
  components: { ChatItem },
    data:()=>{
        return{
            chats:[
                'a', 'b','c'
            ]
        }
    }
}
</script>

<style scoped>
.chats{    
    position:fixed;
    width: 25%;
    background-color: chocolate;
    height: 100%;
    top: 0;
    left: 0;
}
</style>