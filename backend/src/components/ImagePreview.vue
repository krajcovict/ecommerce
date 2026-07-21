<template>
    <div class="flex flex-wrap gap-1">
        <div v-for="image in imageUrls" class="relative w-30 h-30 rounded-md border-2 flex items-center justify-center text-white border-gray-300 hover:border-purple-500 overflow-hidden">
            <img :src="image.url" class="h-full w-full object-cover">
            <span class="absolute top-1 right-1 cursor-pointer rounded bg-purple-500" @click="removeImage(image)">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                </svg>
            </span>
        </div>
        <div class="relative w-30 h-30 rounded-md border-2 border-dashed flex items-center justify-center border-gray-300 hover:border-purple-500 overflow-hidden">
            <span>
                Upload
            </span>
            <input type="file" class="absolute left-0 top-0 bottom-0 right-0 w-full h-full opacity-0" @change="onFileChange" multiple>
        </div>
    </div>
</template>

<script setup>
import { onMounted, ref } from 'vue';
import { v4 as uuidv4 } from 'uuid';

// Refs
const files = ref([])
const imageUrls = ref([])

// Props & Emit
const props = defineProps(['modelValue'])
const emit = defineEmits(['update:modelValue'])

// Methods
function onFileChange($event) {
    const chosenFiles = [...$event.target.files]
    files.value = [...files.value, ...chosenFiles]
    $event.target.value = ''
    for (let file of chosenFiles) {
        file.id = uuidv4()
        readFile(file)
            .then(url => {
                imageUrls.value.push({
                    url,
                    id: file.id
                })
        })
    }
    emit('update:modelValue', files.value)
}

function readFile(file) {
    return new Promise((resolve, reject) => {
        const fileReader = new FileReader()
        fileReader.readAsDataURL(file)
        fileReader.onload = () => {
            resolve(fileReader.result)
        }
        fileReader.onerror = reject
    })
}

function removeImage(image) {
    files.value = files.value.filter(f => f.id !== image.id)
    imageUrls.value = imageUrls.value.filter(f => f.id !== image.id)
    emit('update:modelValue', files.value)
}

// Hooks
onMounted(() => {
    emit('update:modelValue', [])
})

</script>
