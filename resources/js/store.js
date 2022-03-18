import Vue from 'vue'
import Vuex from 'vuex'
import axios from 'axios'
Vue.use(Vuex)

export default new Vuex.Store({
    state: {
        status: '',
        token: localStorage.getItem('token') || '',
        user: {},
        selectedChat:null,
        chats:[]
    },
    mutations: {
        auth_request(state) {
            state.status = 'loading'
        },
        auth_success(state, token, user) {
            state.status = 'success'
            state.token = token
            state.user = user
        },
        auth_error(state) {
            state.status = 'error'
        },
        logout(state) {
            state.status = ''
            state.token = ''
            state.is_anouncer = false
        },
        set_user(state, value) {
            state.user = value;
        },
        set_chats(state, chats){
          state.chats = chats;  
        },
        set_chat(state, value) {
            state.chats[
                state.chats.indexOf(
                    state.chats.find((chat) => chat.id == value)
                )
            ].unread = 0;
            state.selectedChat = value;
        },
        set_unread(state, data){
            state.chats[
                state.chats.indexOf(
                    state.chats.find((chat) => chat.id == data.sender)
                )
            ].unread += data.count;
        }
    },
    actions: {
        login({ commit }, user) {
            return new Promise((resolve, reject) => {
                commit('auth_request')
                axios({ url: '/api/login', data: user, method: 'POST' })
                    .then(resp => {
                        const token = resp.data.token
                        const user = resp.data.user
                        localStorage.setItem('token', token)
                        localStorage.setItem('useerid', user.id)
                        axios.defaults.headers.common['Authorization'] = `Bearer ${token}`
                        commit('auth_success', token, resp.data.user)
                        commit('set_user', user)
                        resolve(resp)
                    })
                    .catch(err => {
                        commit('auth_error')
                        localStorage.removeItem('token')
                        reject(err)
                    })
            })
        },
        register({ commit }, user) {
            return new Promise((resolve, reject) => {
                commit('auth_request')
                axios({ url: '/api/register', data: user, method: 'POST' })
                    .then(resp => {
                        const token = resp.data.token
                        const user = resp.data.user
                        localStorage.setItem('token', token)
                        axios.defaults.headers.common['Authorization'] = `Bearer ${token}`
                        commit('auth_success', token, user)
                        commit('set_user', resp.data.user)
                        resolve(resp)
                    })
                    .catch(err => {
                        commit('auth_error', err)
                        localStorage.removeItem('token')
                        reject(err)
                    })
            })
        },
        logout({ commit }) {
            return new Promise((resolve, reject) => {
                axios({ url: '/api/logout', method: 'POST' })
                commit('logout')
                localStorage.removeItem('userid')
                localStorage.removeItem('token')
                Echo.disconnect();
                delete axios.defaults.headers.common['Authorization']
                resolve()
            })
        },
        getUser({commit}){
            return new Promise((resolve, reject) => {
                axios({ url: '/api/user', method: 'GET' }).then(resp =>{
                    commit('set_user', resp.data)  
                })                        
                resolve()
            })
        },
        selectChat({commit}, selectChat){

            commit('set_chat',selectChat);
        },
        getChats({commit}){
            return new Promise((resolve, reject) => {
            axios.get("/api/chats").then((resp) => {
                commit('set_chats',resp.data)
                resolve(resp)
            });
          });
        },
        setUnread({commit}, data){
            commit('set_unread',data);
        }
    },
    getters: {
        isLoggedIn: state => !!state.token,
        authStatus: state => state.status,
        user: state => state.user,
        chats: state=>state.chats,
        selectedChat: state => state.selectedChat,
    }
})