<template>
    <div>
        <div class="chat">

        </div>
    </div>
</template>

<script>
export default {
    props: ['chat']
}
</script>

<style scoped>
.chat{
    min-height: 50px;
    border-radius: 10px;
    margin: 5px;
    width: 97%;
    background: blue;
    left: 0;
}
</style>