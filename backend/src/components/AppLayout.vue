<template>
  <div v-if="currentUser" class="flex min-h-full rounded-lg bg-gray-200">
    <Sidebar :class="{ '-ml-[200px]' : !sidebarOpened}" />
    <div class="flex-1">
        <Navbar @toggle-sidebar="toggleSidebar"/>
        <main class="p-5">
            <div class="p-4 rounded bg-white">
              <router-view></router-view>
            </div>
        </main>
    </div>
  </div>
  <div v-else class="min-h-full flex items-center justify-center bg-gray-200">
    <svg
    class="h-10 w-10 size-5 animate-spin text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
        </circle>
        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
        </path>
    </svg>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted, computed } from 'vue';
import Sidebar from './Sidebar.vue';
import Navbar from './Navbar.vue';
import store from '../store';

const {title} = defineProps({
    title: String
});

const sidebarOpened = ref(true);
const currentUser = computed(() => store.state.user.data);

function toggleSidebar() {
    sidebarOpened.value = !sidebarOpened.value;
}
onMounted(() => {
    store.dispatch('getUser');
    handleResize();
    window.addEventListener('resize', handleResize);
});
onUnmounted(() => {
    window.removeEventListener('resize', handleResize);
});

function handleResize() {
    sidebarOpened.value = window.innerWidth > 768;
}
</script>

<style scoped>

</style>
