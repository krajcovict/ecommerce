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
    <Spinner />
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted, computed } from 'vue';
import Sidebar from './Sidebar.vue';
import Navbar from './Navbar.vue';
import store from '../store';
import Spinner from './core/Spinner.vue';

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
