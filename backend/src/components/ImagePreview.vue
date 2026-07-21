<template>
    <div class="flex flex-wrap gap-1">
        <div v-for="image in imageUrls" class="relative w-30 h-30 rounded-md border-2 flex items-center justify-center text-white border-gray-300 hover:border-purple-500 overflow-hidden">
            <img :src="image" class="h-full w-full object-cover">
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
import { ref } from 'vue';

// Refs
const files = ref([])
const imageUrls = ref([])

// Methods
function onFileChange($event) {
    files.value = [...files.value, ...$event.target.files]
    for (let file of $event.target.files) {
        readFile(file)
            .then(url => {
                imageUrls.value.push(url)
        })

    }
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

</script>
