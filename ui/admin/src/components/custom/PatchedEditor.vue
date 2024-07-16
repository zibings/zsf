<template>
	<Editor @load="onLoad" @update:model-value="onChange" />
</template>

<script setup>
import Editor from "primevue/editor";

const props = defineProps({
	modelValue: {
		type: String,
		default: "",
	},
});

const emits = defineEmits(["update:modelValue"]);

const onLoad = ({ instance }) => {
	instance.setContents(
		instance.clipboard.convert({
			html: props.modelValue,
		}),
	);

	return;
};

const onChange = (v) => {
	emits("update:modelValue", v);

	return;
};
</script>

<style scoped></style>
