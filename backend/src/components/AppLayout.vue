<template>
  <div class="flex min-h-full">
    <Sidebar/>
    <div class="flex-1">
        <TopHeader/>
        <main>
            <router-view></router-view>
        </main>
    </div>
  </div>
</template>

<script setup>
import Sidebar from './Sidebar.vue';
import TopHeader from './TopHeader.vue';

const {title} = defineProps({
    title: String
});
</script>

<style scoped>

</style>
