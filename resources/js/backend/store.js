import { reactive } from 'vue';

// What Vuex held: whether the session is known to be logged in
export default reactive({
  isLoggedIn: false,
});
