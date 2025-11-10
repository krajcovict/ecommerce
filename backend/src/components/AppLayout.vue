<template>
  <div class="flex min-h-full rounded-lg bg-gray-200">
    <Sidebar :class="{ '-ml-[205px]' : !sidebarOpened}" />
    <div class="flex-1">
        <Navbarr @toggle-sidebar="toggleSidebar"/>
        <main class="p-5">
            <div class="p-4 rounded bg-white">
              <router-view></router-view>
            </div>
        </main>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue';
import Sidebar from './Sidebar.vue';
import Navbarr from './Navbar.vue';

const {title} = defineProps({
    title: String
});

const sidebarOpened = ref(true);

function toggleSidebar() {
    sidebarOpened.value = !sidebarOpened.value;
}
onMounted(() => {
    /* store.dispatch('getUser'); */
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
