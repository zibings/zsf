<template>
	<Editor v-bind="props" @load="onLoad" @update:model-value="onChange" />
</template>

<script setup>
import Editor from "primevue/editor";
import BaseEditor from "primevue/editor/BaseEditor.vue";

const props = defineProps({
	...BaseEditor.props,
});

const model = defineModel({ type: String, default: "" });

const onLoad = ({ instance }) => {
	instance.setContents(instance.clipboard.convert({ html: model.value }));

	return;
};

const onChange = (v) => {
	model.value = v;
};
</script>

<style scoped></style>
