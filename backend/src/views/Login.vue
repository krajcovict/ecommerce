<template>
  <GuestLayout title="Sign in to your account">
    <form class="space-y-6" method="POST" @submit.prevent="login">
        <div v-if="errorMsg" class="flex items-center justify-between py-3 px-5 text-sm/6 font-medium rounded-md border border-red-400 bg-red-200 p-2 text-red-700">
            {{ errorMsg }}
            <span @click="errorMsg = ''"
            class="cursor-pointer w-8 h-8 ml-3 flex items-center justify-center
            rounded-full transition-colors duration-500 bg-red-300 text-red-700 font-bold
            hover:bg-red-400">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-6">
                  <path fill-rule="evenodd" d="M12 2.25c-5.385 0-9.75 4.365-9.75 9.75s4.365 9.75 9.75 9.75 9.75-4.365 9.75-9.75S17.385 2.25 12 2.25Zm-1.72 6.97a.75.75 0 1 0-1.06 1.06L10.94 12l-1.72 1.72a.75.75 0 1 0 1.06 1.06L12 13.06l1.72 1.72a.75.75 0 1 0 1.06-1.06L13.06 12l1.72-1.72a.75.75 0 1 0-1.06-1.06L12 10.94l-1.72-1.72Z" clip-rule="evenodd" />
                </svg>
            </span>
        </div>
        <div>
          <label for="email" class="block text-sm/6 font-medium text-gray-900">
            Email address
        </label>
          <div class="mt-2">
            <input type="email" name="email" id="email"
            autocomplete="email" required="" v-model="user.email"
            class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6" />
          </div>
        </div>

        <div>
          <div class="flex items-center justify-between">
            <label for="password" class="block text-sm/6 font-medium text-gray-900">
                Password
            </label>
            <div class="text-sm">
              <router-link :to="{name: 'requestPassword'}" class="font-semibold text-indigo-600 hover:text-indigo-500">Forgot password?</router-link>
            </div>
          </div>
          <div class="mt-2">
            <input type="password" name="password" id="password"
            autocomplete="current-password" required="" v-model="user.password"
            class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6" />
          </div>
        </div>

        <div class="flex items-center">
          <input id="remember-me" name="remember-me" type="checkbox" v-model="user.remember"
          class="h-4 w-4 border-gray-300" />
          <label for="remember-me" class="ml-2 block text-sm/6 text-gray-900">
            Remember me
          </label>
        </div>

        <div>
          <button type="submit"
          :disabled="loading"
          class="flex w-full justify-center rounded-md bg-indigo-600 px-3 py-1.5 text-sm/6 font-semibold text-white shadow-xs hover:bg-indigo-500 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600"
          :class="{'opacity-60 cursor-not-allowed': loading}"
          >
<svg v-if="loading"
class="mr-3 -ml-1 size-5 animate-spin text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
    </circle>
    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
    </path>
</svg>
            Sign in
          </button>
        </div>
    </form>
   </GuestLayout>
</template>

<script setup>
import { ref } from "vue";
import GuestLayout from "../components/GuestLayout.vue";
import { useRouter } from "vue-router";
import store from "../store";

const router = useRouter();

let loading = ref(false);
let errorMsg = ref('');

const user = {
    email: '',
    password: '',
    remember: false
}

function login() {
    loading.value = true;
    store.dispatch('login', user)
        .then(() => {
            loading.value = false;
            router.push({ name: 'app.dashboard' });
        })
        .catch(({response}) => {
            loading.value = false;
            errorMsg.value = response.data.message || 'An error occurred during login.';
        });
}
</script>

<style scoped>

</style>
