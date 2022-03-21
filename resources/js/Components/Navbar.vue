<template>
    <div class="navi">
        <div class="menu">
            <v-menu offset-y bottom left>
                <template v-slot:activator="{ on, attrs }">
                    <v-btn large dark icon v-bind="attrs" v-on="on">
                        <v-icon>mdi-menu</v-icon>
                    </v-btn>
                </template>
                <v-list>
                    <v-list-item @click="logout"> Log out </v-list-item>
                </v-list>
            </v-menu>
        </div>

        <div class="search">
            <v-text-field
                rounded
                dark
                single-line
                filled
                clearable
                append-icon="search"
                @click:append="getIssues"
                height="44px"
                hide-details=""
                dense
            ></v-text-field>
        </div>
    </div>
</template>

<script>
export default {
    methods: {
        logout() {
            this.$store.dispatch("logout").then(() => {
                this.$router.push("/login");
            });
        },
        getIssues() {},
    },
};
</script>

<style scoped>
.navi {
    position: fixed !important;
    height: 8%;
    width: 25%;
    top: 0;
    left: 0;
    background: rgb(33, 33, 33);
    border-bottom: 2px solid rgb(0, 0, 0);
    display: flex;
    min-height: 58px;
}

@media (max-width: 700px) {
    .navi {
        width: 80px;
    }
}
.search {
    margin-top: 2%;
    width: 90%;
}
.menu {
    margin-top: 2%;
    max-height: 40px;
}
</style>
