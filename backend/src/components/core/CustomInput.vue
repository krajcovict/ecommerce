<template>
    <div>
        <label class="sr-only">{{ label }}</label>
        <div class="mt-1 flex rounded-md shadow-sm">
            <span v-if="prepend"
            class="inline-flex items-center px-3 rounded-l-md border border-r-0 border-gray-300 bg-gray-50 text-gray-500 text-sm">
                {{ prepend }}
            </span>
            <template v-if="type === 'textarea'">
                <textarea :name="name"
                :required="required"
                :value="props.modelValue"
                @input="emit('update:modelValue', $event.target.value)"
                :class="inputClasses"
                :placeholder="label"
                ></textarea>
            </template>
            <template v-else-if="type === 'richtext'">
                <ckeditor
                    v-if="canRenderRichtext"
                    :required="required"
                    v-model="richtextValue"
                    :class="inputClasses"
                    :editor="editor"
                    :config="config"
                />
            </template>
            <template v-else-if="type === 'select'">
                <select :name="name"
                :required="required"
                :value="props.modelValue"
                @change="onChange($event.target.value)"
                :class="inputClasses"
                >
                    <option v-for="option in selectOptions" :value="option.key">
                        {{ option.text }}
                    </option>
                </select>
            </template>
            <template v-else-if="type === 'file'">
                <input :type="type"
                :name="name"
                :required="required"
                @change="onFileChange"
                :class="inputClasses"
                :placeholder="label"/>
            </template>
            <template v-else-if="type === 'checkbox'">
                <input :id="id"
                :type="type"
                :name="name"
                :required="required"
                :checked="props.modelValue"
                @change="emit('update:modelValue', $event.target.checked)"
                :class="inputClasses"/>
                <label :for="id" class="ml-2 block text-sm/6 text-gray-900">
                  {{label}}
                </label>
            </template>
            <template v-else>
                <input :type="type"
                :name="name"
                :required="required"
                :value="props.modelValue"
                @input="emit('update:modelValue', $event.target.value)"
                :class="inputClasses"
                :placeholder="label"
                :step="props.step"
                min="0"/>
            </template>
            <span v-if="append" class="inline-flex items-center px-3 rounded-r-md border border-l-0 border-gray-300 bg-gray-50 text-gray-500 text-sm">
                {{ append }}
            </span>
        </div>
        <small v-if="errors && errors[0]" class="text-red-600 ml-2">{{ errors[0] }}</small>
    </div>
</template>

<script setup>
import { computed } from 'vue';
import { Ckeditor, useCKEditorCloud } from '@ckeditor/ckeditor5-vue';

const props = defineProps({
    modelValue: [String, Number, Boolean, File],
    label: String,
    type: {type: String, default: 'text' },
    name: String,
    required: Boolean,
    step: [String, Number],
    prepend: {type: String, default: '' },
    append: { type: String, default: '' },
    selectOptions: Array,
    errors: {type: Array, required: false},
})

const id = computed(() => {
    if (props.id) return props.id
    return `id-${Math.floor(1000000 + Math.random() * 1000000)}`
})

const inputClasses = computed(() => {
    const cls = [
        `block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm file:bg-indigo-200 file:border-1 file:px-4 file:rounded`
    ];

    if (props.append && !props.prepend) {
        cls.push(`rounded-l-md`)
    } else if (props.prepend && !props.append) {
        cls.push(`rounded-r-md`)
    } else if (!props.prepend && !props.append) {
        cls.push(`rounded-md`)
    }

    if (props.errors && props.errors[0]) {
        cls.push('border-red-600 focus:border-red-600')
    }

    return cls.join(' ')
})

const emit = defineEmits(['update:modelValue', 'change'])

function onChange(value) {
    emit('update:modelValue', value)
    emit('change', value)
}

function onFileChange(event) {
    const file = event.target.files && event.target.files[0] ? event.target.files[0] : null
    emit('update:modelValue', file)
    emit('change', file)
}

// CKEditor component:

const cloud = useCKEditorCloud( {
    version: '48.3.0',
    premium: true
} );

const editor = computed( () => {
    if ( !cloud.data.value ) {
        return null;
    }

    return cloud.data.value.CKEditor.ClassicEditor;
} );

const config = computed( () => {
        if ( !cloud.data.value ) {
        return null;
    }

    const { Essentials, Paragraph, Bold, Italic } = cloud.data.value.CKEditor;
    const { FormatPainter } = cloud.data.value.CKEditorPremiumFeatures;

    return {
        licenseKey: import.meta.env.VITE_CKEDITOR_PRODUCTION_KEY,
        plugins: [ Essentials, Paragraph, Bold, Italic ],
        toolbar: [ 'undo', 'redo', '|', 'bold', 'italic' ]
    };
} );

const canRenderRichtext = computed(() => props.type === 'richtext' && !!editor.value && !!config.value)

const richtextValue = computed({
    get: () => props.modelValue ?? '',
    set: (value) => onChange(value)
})

</script>

<style scoped>
:deep(.ck-editor) { width: 100%; }
:deep(.ck-content) { min-height: 200px; }
</style>
