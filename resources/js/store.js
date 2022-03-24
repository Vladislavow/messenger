import Vue from "vue";
import Vuex from "vuex";
import axios from "axios";
Vue.use(Vuex);

export default new Vuex.Store({
    state: {
        status: "",
        token: localStorage.getItem("token") || "",
        user: {},
        selectedChat: null,
        chats: [],
        selectedProfile: null,
        search: false,
        searchedChats: null,
    },
    mutations: {
        auth_request(state) {
            state.status = "loading";
        },
        auth_success(state, token, user) {
            state.status = "success";
            state.token = token;
            state.user = user;
        },
        auth_error(state) {
            state.status = "error";
        },
        logout(state) {
            state.status = "";
            state.token = "";
            state.is_anouncer = false;
        },
        set_user(state, value) {
            state.user = value;
        },
        set_chats(state, chats) {
            state.chats = chats;
        },
        set_chat(state, value) {
            var chat =
                state.chats[
                    state.chats.indexOf(
                        state.chats.find((chat) => chat.id == value)
                    )
                ];
            if (state.search && !chat) {
                console.log("hi");
                state.chats.unshift(
                    state.searchedChats[
                        state.searchedChats.indexOf(
                            state.searchedChats.find((chat) => chat.id == value)
                        )
                    ]
                );
                state.search = false;
                state.selectedChat = value;
                return;
            }
            chat.unread = 0;
            state.selectedChat = value;
        },
        set_unread(state, data) {
            state.chats[
                state.chats.indexOf(
                    state.chats.find((chat) => chat.id == data.sender)
                )
            ].unread += data.count;
        },
        set_last_message(state, data) {
            state.chats[
                state.chats.indexOf(
                    state.chats.find((chat) => chat.id == data.sender)
                )
            ].last_message = data.message;
        },
        set_selected_profile(state, value) {
            state.selectedProfile = value;
        },
        set_search(state, value) {
            state.search = value;
        },
        set_searched_chats(state, value) {
            state.searchedChats = value;
        },
        check_exists(state, chat) {
            if (!state.chats.find((chat_a) => chat_a.id == chat.id)) {
                chat.unread = 1;
                state.chats.push(chat);
            } else {
                state.chats[
                    state.chats.indexOf(
                        state.chats.find((chat_a) => chat_a.id == chat.id)
                    )
                ].unread += 1;
            }
        },
        update_chat(state, chat) {
            state.chats.splice(
                state.chats[
                    state.chats.indexOf(
                        state.chats.find((chat_a) => chat_a.id == chat.id)
                    )
                ],
                1
            );
            state.chats.push(chat);
        },
    },
    actions: {
        login({ commit }, user) {
            return new Promise((resolve, reject) => {
                commit("auth_request");
                axios({ url: "/api/login", data: user, method: "POST" })
                    .then((resp) => {
                        const token = resp.data.token;
                        const user = resp.data.user;
                        localStorage.setItem("token", token);
                        localStorage.setItem("userid", user.id);
                        axios.defaults.headers.common[
                            "Authorization"
                        ] = `Bearer ${token}`;
                        commit("auth_success", token, resp.data.user);
                        commit("set_user", user);
                        resolve(resp);
                    })
                    .catch((err) => {
                        commit("auth_error");
                        localStorage.removeItem("token");
                        reject(err);
                    });
            });
        },
        register({ commit }, data) {
            return new Promise((resolve, reject) => {
                commit("auth_request");
                axios
                    .post("/api/register", data.form, data.config)
                    .then((resp) => {
                        const token = resp.data.token;
                        const user = resp.data.user;
                        localStorage.setItem("token", token);
                        localStorage.setItem("userid", user.id);
                        axios.defaults.headers.common[
                            "Authorization"
                        ] = `Bearer ${token}`;
                        commit("auth_success", token, user);
                        commit("set_user", resp.data.user);
                        resolve(resp);
                    })
                    .catch((err) => {
                        commit("auth_error", err);
                        localStorage.removeItem("token");
                        reject(err);
                    });
            });
        },
        logout({ commit }) {
            return new Promise((resolve, reject) => {
                axios({ url: "/api/logout", method: "POST" });
                commit("logout");
                localStorage.removeItem("userid");
                localStorage.removeItem("token");
                Echo.disconnect();
                delete axios.defaults.headers.common["Authorization"];
                resolve();
            });
        },
        getUser({ commit }) {
            return new Promise((resolve, reject) => {
                axios({ url: "/api/user", method: "GET" }).then((resp) => {
                    commit("set_user", resp.data);
                });
                resolve();
            });
        },
        selectChat({ commit }, selectChat) {
            commit("set_chat", selectChat);
        },
        getChats({ commit }) {
            return new Promise((resolve, reject) => {
                axios.get("/api/chats").then((resp) => {
                    commit("set_chats", resp.data);
                    resolve(resp);
                });
            });
        },
        setUnread({ commit }, data) {
            commit("set_unread", data);
        },
        setLastMessage({ commit }, data) {
            commit("set_last_message", data);
        },
        setSelectedProfile({ commit }, value) {
            commit("set_selected_profile", value);
        },
        setSearch({ commit }, value) {
            commit("set_search", value);
        },
        setSearchedChats({ commit }, value) {
            commit("set_searched_chats", value);
        },
        checkForChatExists({ commit }, message) {
            axios.get("/api/chat/" + message.sender).then((response) => {
                commit("check_exists", response.data);
            });
        },
        updateChat({ commit }, chat) {
            axios.get("/api/chat/" + chat.id).then((response) => {
                commit("update_chat", response.data);
            });
        },
    },
    getters: {
        isLoggedIn: (state) => !!state.token,
        authStatus: (state) => state.status,
        user: (state) => state.user,
        chats: (state) => state.chats,
        selectedChat: (state) => state.selectedChat,
        selectedProfile: (state) => state.selectedProfile,
        selectedChatInfo: (state) =>
            state.chats[
                state.chats.indexOf(
                    state.chats.find((chat) => chat.id == state.selectedChat)
                )
            ],
        search: (state) => state.search,
        searchedChats: (state) => state.searchedChats,
    },
});
