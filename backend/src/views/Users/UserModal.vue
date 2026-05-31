<template>
  <TransitionRoot appear :show="show" as="template">
    <Dialog as="div" @close="closeModal" class="relative z-10">
      <TransitionChild
        as="template"
        enter="duration-300 ease-out"
        enter-from="opacity-0"
        enter-to="opacity-100"
        leave="duration-200 ease-in"
        leave-from="opacity-100"
        leave-to="opacity-0"
      >
        <div class="fixed inset-0 bg-black/60"></div>
      </TransitionChild>

      <div class="fixed inset-0 overflow-y-auto">
        <div
          class="flex min-h-full items-center justify-center p-4 text-center"
        >
          <TransitionChild
            as="template"
            enter="duration-300 ease-out"
            enter-from="opacity-0 scale-95"
            enter-to="opacity-100 scale-100"
            leave="duration-200 ease-in"
            leave-from="opacity-100 scale-100"
            leave-to="opacity-0 scale-95"
          >
            <DialogPanel
              class="w-full max-w-md transform overflow-hidden rounded-2xl bg-white text-left align-middle shadow-xl transition-all"
            >
              <header class="py-3 px-4 flex justify-between items-center">
                <DialogTitle>
                    {{ user.id ? `Update user: "${props.user.name}"` : 'Create new User' }}
                </DialogTitle>

                <button
                    @click="closeModal"
                    class="w-8 h-8 flex justify-center rounded-full transition-colors cursor-pointer hover:bg-[rgba(0,0,0,0.2)]"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                    </svg>
                </button>
              </header>
              <Spinner v-if="loading"
              class="absolute place-self-center flex items-center justify-center"/>

              <form @submit.prevent="onSubmit">
                <div class="bg-white px-4 pt-5 pb-4">
                    <CustomInput class="mb-2" v-model="user.name" label="User Name" />
                    <CustomInput class="mb-2" v-model="user.email" label="User Email" />
                    <CustomInput type="password" class="mb-2" v-model="user.password" label="User Password" />
                </div>
                <footer class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse">
                    <button type="submit"
                    class="w-full py-2 px-4 sm:ml-3 border border-gray-300 rounded-md bg-indigo-600 text-white shadow-sm hover:bg-indigo-700 mt-3"
                    >
                        Submit
                    </button>
                    <button type="button"
                    class="w-full py-2 px-4 border border-gray-300 rounded-md shadow-sm hover:bg-[rgba(0,0,0,0.2)] mt-3"
                    @click="closeModal" ref="cancelButtonRef"
                    >
                        Cancel
                    </button>

                </footer>
              </form>


            </DialogPanel>
          </TransitionChild>
        </div>
      </div>
    </Dialog>
  </TransitionRoot>
</template>

<script setup>
import { ref, computed, onUpdated } from 'vue'

import {
  TransitionRoot,
  TransitionChild,
  Dialog,
  DialogPanel,
  DialogTitle,
} from '@headlessui/vue'

import store from '../../store/index.js';

import Spinner from '../../components/core/Spinner.vue';
import CustomInput from '../../components/core/CustomInput.vue';

const loading = ref(false)

const props = defineProps({
    modelValue: Boolean,
    user: {
        required: true,
        type: Object
    }
})

const user = ref({
    id: props.user.id,
    name: props.user.name,
    email: props.user.email,
})

const emit = defineEmits(['update:modelValue'])

const show = computed({
  get: () => props.modelValue,
  set: (value) => emit('update:modelValue', value)
})

onUpdated (() => {
    user.value = {
        id: props.user.id,
        name: props.user.name,
        email: props.user.email,
        description: props.user.description,
    }
})

function closeModal() {
    show.value = false
    emit('close')
}

function onSubmit() {
    loading.value = true
    if (user.value.id) {
        store.dispatch('updateUser', user.value)
            .then(response => {
                loading.value = false;
                if (response.status === 200) {
                    // TODO show notification
                    store.dispatch('getUsers')
                    closeModal()
                }
        })
    } else {
        store.dispatch('createUser', user.value)
            .then(response => {
                loading.value = false
                if (response.status === 201) {
                    // TODO show notification
                    store.dispatch('getUsers')
                closeModal()
            }
        })
    }
}
</script>
