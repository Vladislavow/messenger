<template>
    <div class="messages" ref="container" id="mes">
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
            rounded
        >
            Load more
        </v-btn>
        <template class="messages" v-for="(message, index) in this.messages">
            <div v-if="checkDate(message.created_at, index)" class="date">
                {{ dates.get(index) }}
            </div>
            <message :message="message" :userId="userId" :key="index" />
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
        };
    },
    components: { Message },
    props: ["messages", "userId", "laoding", "page", "totalPages"],
    watch: {
        messages: function () {
            setTimeout(() => {
                if (this.scrollLock) {
                    this.scrollLock = false;
                    return;
                }
                var container = document.getElementById("mes");
                container.scrollTop =
                    container.scrollHeight - container.clientHeight;
                this.$emit("loaded");
            }, 50);
        },
    },
    methods: {
        getMessages() {
            this.scrollLock = true;
            this.$emit("getMessages");
        },
        checkDate(date, index) {
            let current = new Date(date).toDateString();
            if (index == 0) {
                this.dates.set(index, current);
                return true;
            } else {
                let prev = new Date(
                    this.messages[index - 1].created_at
                ).toDateString();
                if (prev != current) {
                    this.dates.set(index, current);
                    return true;
                }
            }
        },
        async openSimpleSlide(index) {
            let prev = -1;
            for (let key of this.dates.keys()) {
                if (key == index) {
                    break;
                }
                prev = key;
            }
            // Получение ссылки на элемент
            let slide = this.$refs[`date-0`];
            // Определение расстояния от начала страницы до нужного элемента
            let top = window.scrollY + slide.getBoundingClientRect().y;
            // Перемотка
            window.scrollTo(0, top);
        },
    },
    mounted() {},
};
</script>

<style scoped>
.messages {
    position: fixed;
    width: 50%;
    height: 85%;
    background: rgb(33, 33, 33);
    overflow: scroll;
    overflow-x: hidden;
    display: flex;
    flex-direction: column;
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
</style>
