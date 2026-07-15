<template>
    <ckeditor
        v-if="editor"
        v-model="data"
        @input="onChange"
        :editor="editor"
        :config="config"
    />
</template>

<script setup>
import { ref, computed } from 'vue';
import { Ckeditor, useCKEditorCloud } from '@ckeditor/ckeditor5-vue';

const cloud = useCKEditorCloud( {
    version: '48.3.0',
    premium: true
} );

const data = ref( '<p>Hello world!</p>' );

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
        licenseKey: import.meta.env.VITE_CKEDITOR_DEVELOPMENT_KEY,
        plugins: [ Essentials, Paragraph, Bold, Italic, FormatPainter ],
        toolbar: [ 'undo', 'redo', '|', 'bold', 'italic', '|', 'formatPainter' ]
    };
} );
</script>
