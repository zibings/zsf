<template>
	<div :class="containerClass" @click="onClick($event)">
		<div class="p-hidden-accessible">
			<input
				ref="input"
				:id="inputId"
				type="checkbox"
				role="switch"
				:class="inputClass"
				:style="inputStyle"
				:checked="checked"
				:disabled="disabled"
				:aria-checked="checked"
				:aria-labelledby="ariaLabelledby"
				:aria-label="ariaLabel"
				@focus="onFocus($event)"
				@blur="onBlur($event)"
				v-bind="inputProps"
			/>
		</div>
		<span class="p-inputswitch-slider">
			<i class="pi pi-sun w-6 h-6 dark-toggle-sun"></i>
			<i class="pi pi-moon w-6 h-6 dark-toggle-moon"></i>
		</span>
	</div>
</template>

<script setup>
import { computed, ref } from "vue";

const input = ref(null);
const focused = ref(false);
const $emit = defineEmits(["update:modelValue", "click", "change", "input", "focus", "blur"]);

const props = defineProps({
	modelValue: {
		type: null,
		default: false,
	},
	trueValue: {
		type: null,
		default: true,
	},
	falseValue: {
		type: null,
		default: false,
	},
	disabled: {
		type: Boolean,
		default: false,
	},
	inputId: {
		type: String,
		default: null,
	},
	inputClass: {
		type: String,
		default: null,
	},
	inputStyle: {
		type: null,
		default: null,
	},
	inputProps: {
		type: null,
		default: null,
	},
	ariaLabelledby: {
		type: String,
		default: null,
	},
	ariaLabel: {
		type: String,
		default: null,
	},
});

function onClick(event) {
	if (!props.disabled) {
		const newValue = checked.value ? props.falseValue : props.trueValue;

		$emit("click", event);
		$emit("update:modelValue", newValue);
		$emit("change", event);
		$emit("input", newValue);
		input.value.focus();
	}

	event.preventDefault();
}

function onFocus(event) {
	focused.value = true;
	$emit("focus", event);
}

function onBlur(event) {
	focused.value = false;
	$emit("blur", event);
}

const containerClass = computed(() => {
	return [
		"p-inputswitch p-component",
		{
			"p-inputswitch-checked": checked.value,
			"p-disabled": props.disabled,
			"p-focus": focused,
		},
	];
});

const checked = computed(() => {
	return props.modelValue === props.trueValue;
});
</script>

<style lang="scss" scoped>
.p-inputswitch {
	position: relative;
	display: inline-block;

	&.p-inputswitch-checked {
		.dark-toggle-sun {
			opacity: 0;
		}
		.dark-toggle-moon {
			opacity: 1;
		}
	}

	.dark-toggle-sun {
		opacity: 1;
	}
	.dark-toggle-moon {
		opacity: 0;
	}
}

.p-inputswitch-slider {
	position: absolute;
	cursor: pointer;
	top: 0;
	left: 0;
	right: 0;
	bottom: 0;
}

.dark-toggle-sun {
	position: relative;
	font-size: 0.9rem;
	top: 5%;
	left: 14%;
}
.dark-toggle-moon {
	color: #fff;
	position: relative;
	font-size: 0.85rem;
	right: -7%;
	top: 4%;
}

.p-inputswitch-slider:before {
	position: absolute;
	content: "";
	top: 50%;
	background-color: var(--surface-ground) !important;
}
</style>
