<template>
    <header class="h-14 flex justify-between items-center shadow rounded-tr-lg bg-white">
      <button
      class="w-13 h-14 text-gray-700 flex items-center justify-center
      hover:bg-black/20 transition-colors duration-400"
      @click="emit('toggle-sidebar')">
        <Bars4Icon class="w-5"/>
      </button>
      <Menu as="div" class="px-4 justify-center">
          <div>
            <MenuButton class="flex items-center justify-center">
              <img src="https://randomuser.me/api/portraits/women/68.jpg" alt="user avatar"
              class="w-10 rounded-full mr-3"/>
                <small class="text-gray-700">Jane Smith</small>
              <ChevronDownIcon
                class="-mr-1 ml-2 h-5 w-5 text-indigo-300 hover:text-indigo-200"
                aria-hidden="true"
              />
            </MenuButton>
          </div>

          <transition
            enter-active-class="transition duration-100 ease-out"
            enter-from-class="transform scale-95 opacity-0"
            enter-to-class="transform scale-100 opacity-100"
            leave-active-class="transition duration-75 ease-in"
            leave-from-class="transform scale-100 opacity-100"
            leave-to-class="transform scale-95 opacity-0"
          >
            <MenuItems
              class="absolute right-3 mt-2 w-35 origin-top-right divide-y divide-gray-100 rounded-md bg-white shadow-lg ring-1 ring-black/5 focus:outline-none"
            >
              <div class="px-1 py-1">
                <MenuItem v-slot="{ active }">
                  <button
                    :class="[
                      active ? 'bg-indigo-500 text-white' : 'text-gray-900',
                      'group flex w-full items-center rounded-md px-2 py-2 text-sm transition-colors duration-400',
                    ]"
                  >
                    <UserCircleIcon
                      :active="active"
                      class="mr-2 h-5 w-5 text-indigo-400"
                      aria-hidden="true"
                    />
                    Profile
                  </button>
                </MenuItem>
                <MenuItem v-slot="{ active }">
                  <button
                    @click="logout"
                    :class="[
                      active ? 'bg-indigo-500 text-white' : 'text-gray-900',
                      'group flex w-full items-center rounded-md px-2 py-2 text-sm transition-colors duration-400',
                    ]"
                  >
                    <ArrowRightStartOnRectangleIcon
                      :active="active"
                      class="mr-2 h-5 w-5 text-indigo-400"
                      aria-hidden="true"
                    />
                    Logout
                  </button>
                </MenuItem>
              </div>
            </MenuItems>
          </transition>
        </Menu>
    </header>
</template>

<script setup>
import { Bars4Icon, ChevronDownIcon, UserCircleIcon, ArrowRightStartOnRectangleIcon } from '@heroicons/vue/24/outline'
import { Menu, MenuButton, MenuItems, MenuItem } from '@headlessui/vue'
import { useRouter } from 'vue-router';
import store from '../store';

const router = useRouter();

const emit = defineEmits(['toggle-sidebar']);

function logout() {
    store.dispatch('logout')
        .then(() => {
            router.push({ name: 'login' });
        });
}
</script>

<style scoped>

</style>
