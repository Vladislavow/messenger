<template>
    <div class="navi">
        <div class="menu">
            <v-menu offset-y bottom transition="slide-y-transition">
                <template v-slot:activator="{ on, attrs }">
                    <v-btn large dark icon v-bind="attrs" v-on="on">
                        <v-icon>mdi-menu</v-icon>
                    </v-btn>
                </template>
                <v-list dense shaped>
                    <v-list-item @click="showProfile">
                        <v-icon>mdi-account</v-icon>
                        {{ " Profile" }}
                    </v-list-item>
                    <v-list-item @click="logout">
                        <v-icon>mdi-logout</v-icon>{{ " Log out" }}
                    </v-list-item>
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
                label="Search"
                v-model="search"
                @click:clear="stopSearch()"
                @input="getIssues"
                outlined
            >
                <template v-slot:append>
                    <v-progress-circular
                        v-if="loading"
                        size="24"
                        color="info"
                        indeterminate
                    ></v-progress-circular>
                </template>
            </v-text-field>
        </div>
    </div>
</template>

<script>
export default {
    data: () => {
        return {
            search: "",
            cancelToken: null,
            source: null,
            loading: false,
        };
    },
    watch: {
        search: function (value) {
            if (value == "") {
                this.stopSearch();
            }
        },
    },
    methods: {
        logout() {
            this.$store.dispatch("logout").then(() => {
                this.$router.push("/login");
                location.reload();
            });
        },
        getIssues() {
            this.loading = true;
            if (this.source != null) {
                this.source.cancel();
            }
            this.CancelToken = axios.CancelToken;
            this.source = this.CancelToken.source();
            if (this.search) {
                axios
                    .get("/api/chats?title=" + this.search, {
                        cancelToken: this.source.token,
                    })
                    .then((response) => {
                        this.$store.dispatch("setSearch", true);
                        this.$store.dispatch("setSearchedChats", response.data);
                        this.loading = false;
                    });
            } else {
                this.stopSearch();
            }
        },
        showProfile() {
            this.$store.dispatch(
                "setSelectedProfile",
                localStorage.getItem("userid")
            );
        },
        stopSearch() {
            this.$store.dispatch("setSearch", false);
            this.loading = false;
        },
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
    border-right: 1px solid black;
    z-index: 1000;
}

@media (max-width: 700px) {
    .navi {
        width: 80px;
    }
}
.search {
    margin-top: 2%;
    width: 90%;
    margin-right: 5px;
}
.menu {
    margin-top: 2%;
    max-height: 40px;
}
</style>
