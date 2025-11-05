<template>
    <header class="h-14 flex justify-between items-center shadow rounded-tr-lg bg-white">
      <button class="w-5 pl-4">
        <Bars4Icon class="w-5"/>
      </button>
      <div class="px-20"><UserCircleIcon/></div>
      <Menu as="div" class="px-4">
          <div>
            <MenuButton class="flex items-center">
              <img src="https://randomuser.me/api/portraits/women/68.jpg" alt="user avatar" class="w-10 rounded-full"/>
              <ChevronDownIcon
                class="-mr-1 ml-2 h-5 w-5 text-violet-200 hover:text-violet-100"
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
              class="absolute right-3 mt-2 w-56 origin-top-right divide-y divide-gray-100 rounded-md bg-white shadow-lg ring-1 ring-black/5 focus:outline-none"
            >
              <div class="px-1 py-1">
                <MenuItem v-slot="{ active }">
                  <button
                    :class="[
                      active ? 'bg-violet-500 text-white' : 'text-gray-900',
                      'group flex w-full items-center rounded-md px-2 py-2 text-sm',
                    ]"
                  >
                    <EditIcon
                      :active="active"
                      class="mr-2 h-5 w-5 text-violet-400"
                      aria-hidden="true"
                    />
                    Edit
                  </button>
                </MenuItem>
                <MenuItem v-slot="{ active }">
                  <button
                    :class="[
                      active ? 'bg-violet-500 text-white' : 'text-gray-900',
                      'group flex w-full items-center rounded-md px-2 py-2 text-sm',
                    ]"
                  >
                    <DuplicateIcon
                      :active="active"
                      class="mr-2 h-5 w-5 text-violet-400"
                      aria-hidden="true"
                    />
                    Duplicate
                  </button>
                </MenuItem>
              </div>
              <div class="px-1 py-1">
                <MenuItem v-slot="{ active }">
                  <button
                    :class="[
                      active ? 'bg-violet-500 text-white' : 'text-gray-900',
                      'group flex w-full items-center rounded-md px-2 py-2 text-sm',
                    ]"
                  >
                    <ArchiveIcon
                      :active="active"
                      class="mr-2 h-5 w-5 text-violet-400"
                      aria-hidden="true"
                    />
                    Archive
                  </button>
                </MenuItem>
                <MenuItem v-slot="{ active }">
                  <button
                    :class="[
                      active ? 'bg-violet-500 text-white' : 'text-gray-900',
                      'group flex w-full items-center rounded-md px-2 py-2 text-sm',
                    ]"
                  >
                    <MoveIcon
                      :active="active"
                      class="mr-2 h-5 w-5 text-violet-400"
                      aria-hidden="true"
                    />
                    Move
                  </button>
                </MenuItem>
              </div>

              <div class="px-1 py-1">
                <MenuItem v-slot="{ active }">
                  <button
                    :class="[
                      active ? 'bg-violet-500 text-white' : 'text-gray-900',
                      'group flex w-full items-center rounded-md px-2 py-2 text-sm',
                    ]"
                  >
                    <DeleteIcon
                      :active="active"
                      class="mr-2 h-5 w-5 text-violet-400"
                      aria-hidden="true"
                    />
                    Delete
                  </button>
                </MenuItem>
              </div>
            </MenuItems>
          </transition>
        </Menu>
    </header>
</template>

<script setup>
import { Bars4Icon, UserCircleIcon } from '@heroicons/vue/24/outline'

import { Menu, MenuButton, MenuItems, MenuItem } from '@headlessui/vue'
import { ChevronDownIcon } from '@heroicons/vue/20/solid'
</script>

<style scoped>

</style>
