<template>
    <FileUpload :v-model="uploadImageList" :multiple="true" accept="image/*" :maxFileSize="1000000"
        @select="onSelectedFiles">
        <template #header="{ chooseCallback, clearCallback, files }">
            <div class="flex flex-wrap justify-between items-center flex-1 gap-4">
                <div class="flex gap-2">
                    <Button @click="chooseCallback()" icon="pi pi-images" rounded variant="outlined"
                        severity="secondary"></Button>
                    <Button @click="onClearTemplatingUpload(clearCallback)" icon="pi pi-times" rounded
                        variant="outlined" severity="danger" :disabled="!files || files.length === 0"></Button>
                </div>
            </div>
        </template>
        <template #content="{ files, uploadedFiles, removeUploadedFileCallback, removeFileCallback }">
            <div class="flex flex-col gap-8 pt-4">
                <div v-if="files.length > 0">
                    <h5>Pending</h5>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-2">
                        <div v-for="(file, index) of files" :key="file.name + file.type + file.size"
                            class="border border-surface rounded">
                            <div class="w-full aspect-5/6 bg-surface-100 dark:bg-surface-800">
                                <img role="presentation" :alt="file.name" :src="file.objectURL"
                                    class="w-full h-full object-cover" />
                            </div>
                            <div class="p-2 items-center flex flex-col gap-2">
                                <span
                                    class="font-semibold text-ellipsis max-w-60 whitespace-nowrap overflow-hidden">{{ file.name.split(' ')[0] }}</span>
                                <div>{{ formatSize(file.size) }}</div>
                                <Badge value="Pending" severity="warn" />
                                <Button icon="pi pi-times"
                                    @click="onRemoveTemplatingFile(file, removeFileCallback, index)" variant="outlined"
                                    rounded severity="danger" />
                            </div>
                        </div>
                    </div>
                </div>

                <div v-if="uploadedFiles.length > 0">
                    <h5>Completed</h5>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-2">
                        <div v-for="(file, index) of uploadedFiles" :key="file.name + file.type + file.size"
                            class="rounded-border border border-surface">
                            <div class="w-full aspect-4/3 overflow-hidden">
                                <img role="presentation" :alt="file.name" :src="file.objectURL"
                                    class="w-full h-full  object-cover" />
                            </div>
                            <div class="p-4 items-center flex flex-col gap-2">
                                <span
                                    class="font-semibold text-ellipsis max-w-60 whitespace-nowrap overflow-hidden">{{ file.name.split(' ')[0] }}</span>
                                <div>{{ formatSize(file.size) }}</div>
                                <Badge value="Completed" class="mt-4" severity="success" />
                                <Button icon="pi pi-times" @click="removeUploadedFileCallback(index)" variant="outlined"
                                    rounded severity="danger" />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </template>
        <template #empty>
            <div class="flex items-center justify-center flex-col">
                <i class="pi pi-cloud-upload border! border-gray-300! rounded-full! p-8! text-4xl! text-gray-500!" />
                <p class="mt-6 mb-0">Drag and drop files to here to upload.</p>
            </div>
        </template>
    </FileUpload>
</template>

<script setup>
import { ref } from "vue";
import { usePrimeVue } from 'primevue/config';
import FileUpload from 'primevue/fileupload';

const $primevue = usePrimeVue();
const totalSize = ref(0);
const totalSizePercent = ref(0);

const props = defineProps({
    uploadImageList: {
        type: Array,
        required: false
    },
});
const uploadImageList = ref(props.uploadImageList);

const onSelectedFiles = (event) => {
    uploadImageList = event.files.map((file, index) => renameImageFile(file, form.name, index))
};

const onRemoveTemplatingFile = (file, removeFileCallback, index) => {
    removeFileCallback(index);
    totalSize.value -= parseInt(formatSize(file.size));
    totalSizePercent.value = totalSize.value / 10;
    uploadImageList.splice(index, 1);
    if (uploadImageList.length) {
        uploadImageList = uploadImageList.map((image, index) => renameImageFile(image, newName, index))
    }
    console.log(uploadImageList);
};

const onClearTemplatingUpload = (clear) => {
    clear();
    totalSize.value = 0;
    totalSizePercent.value = 0;
};

const formatSize = (bytes) => {
    const k = 1024;
    const dm = 3;
    const sizes = $primevue.config.locale.fileSizeTypes;

    if (bytes === 0) {
        return `0 ${sizes[0]}`;
    }

    const i = Math.floor(Math.log(bytes) / Math.log(k));
    const formattedSize = parseFloat((bytes / Math.pow(k, i)).toFixed(dm));

    return `${formattedSize} ${sizes[i]}`;
};
</script>