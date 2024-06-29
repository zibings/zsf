<template>
	<div :class="containerClass">
		<InputText
			ref="input"
			:id="inputId"
			:type="inputType"
			:class="inputFieldClass"
			:style="inputStyle"
			:value="modelValue"
			:aria-labelledby="ariaLabelledby"
			:aria-label="ariaLabel"
			:aria-controls="(panelProps && panelProps.id) || panelId || panelUniqueId"
			:aria-expanded="overlayVisible"
			:aria-haspopup="true"
			:placeholder="placeholder"
			:required="required"
			@input="onInput"
			@focus="onFocus"
			@blur="onBlur"
			@keyup="onKeyUp"
			@invalid="onInvalid"
			v-bind="inputProps"
		/>
		<i v-if="toggleMask" :class="toggleIconClass" @click="onMaskToggle" />
		<span class="p-hidden-accessible" aria-live="polite">
			{{ infoText }}
		</span>
		<Portal :append-to="appendTo">
			<transition name="p-connected-overlay" @enter="onOverlayEnter" @leave="onOverlayLeave" @after-leave="onOverlayAfterLeave">
				<div v-if="overlayVisible" :ref="overlayRef" :id="panelId || panelUniqueId" :class="panelStyleClass" :style="panelStyle" @click="onOverlayClick" v-bind="panelProps">
					<slot name="header"></slot>
					<slot name="content">
						<div class="p-password-meter">
							<div :class="strengthClass" :style="{ width: pMeter ? pMeter.width : '' }"></div>
						</div>
						<div class="p-password-info">{{ infoText }}</div>
						<span v-if="scorePwdSuggestions.length > 0">
							<Divider />
							<p class="mt-2">Suggestions</p>
							<ul class="pl-2 ml-2 mt-0" style="line-height: 1.5">
								<li v-for="sug of scorePwdSuggestions" :key="sug">{{ sug }}</li>
							</ul>
						</span>
					</slot>
					<slot name="footer"></slot>
				</div>
			</transition>
		</Portal>
	</div>
</template>

<script setup>
import { watchDebounced } from "@vueuse/core";
import InputText from "primevue/inputtext";
import OverlayEventBus from "primevue/overlayeventbus";
import Portal from "primevue/portal";
import { ConnectedOverlayScrollHandler, DomHandler, UniqueComponentId, ZIndexUtils } from "primevue/utils";
import { computed, inject, onBeforeUnmount, onMounted, ref } from "vue";

import { zxcvbnAsync, zxcvbnOptions } from "@zxcvbn-ts/core";
import * as zxcvbnCommonPackage from "@zxcvbn-ts/language-common";
import * as zxcvbnEnPackage from "@zxcvbn-ts/language-en";
import { matcherPwnedFactory } from "@zxcvbn-ts/matcher-pwned";

const options = {
	translations: zxcvbnEnPackage.translations,
	graphs: zxcvbnCommonPackage.adjacencyGraphs,
	dictionary: {
		...zxcvbnCommonPackage.dictionary,
		...zxcvbnEnPackage.dictionary,
	},
};

zxcvbnOptions.setOptions(options);
const matcherPwned = matcherPwnedFactory(fetch, zxcvbnOptions);
zxcvbnOptions.addMatcher("pwned", matcherPwned);

onMounted(() => {
	watchDebounced(
		() => props.modelValue,
		async () => {
			if (!props.modelValue || props.modelValue.length < 1) {
				scorePwd.value = null;
				scorePwdFeedback.value = null;
				scorePwdSuggestions.value = [];
				infoText.value = promptText.value;
				pMeter.value = null;
				return;
			}

			await setPasswordMeter();
		},
		{ debounce: 350, maxWait: 1000 },
	);
});

const $primevue = inject("$primevue");
const $emit = defineEmits(["update:modelValue", "change", "focus", "blur", "invalid"]);

const scorePwd = ref(null);
const scorePwdFeedback = ref(null);
const scorePwdSuggestions = ref([]);

const input = ref(null);
const overlayVisible = ref(false);
const pMeter = ref(null);
const infoText = ref("");
const focused = ref(false);
const unmasked = ref(false);

let mediumCheckRegExp = null,
	strongCheckRegExp = null,
	resizeListener = null,
	scrollHandler = null,
	overlay = null;

const props = defineProps({
	modelValue: {
		type: String,
		default: null,
	},
	promptLabel: {
		type: String,
		default: null,
	},
	mediumRegex: {
		type: String,
		default: "^(((?=.*[a-z])(?=.*[A-Z]))|((?=.*[a-z])(?=.*[0-9]))|((?=.*[A-Z])(?=.*[0-9])))(?=.{6,})", // eslint-disable-line
	},
	strongRegex: {
		type: String,
		default: "^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.{8,})", // eslint-disable-line
	},
	weakLabel: {
		type: String,
		default: null,
	},
	mediumLabel: {
		type: String,
		default: null,
	},
	strongLabel: {
		type: String,
		default: null,
	},
	feedback: {
		type: Boolean,
		default: true,
	},
	appendTo: {
		type: String,
		default: "body",
	},
	toggleMask: {
		type: Boolean,
		default: false,
	},
	hideIcon: {
		type: String,
		default: "pi pi-eye-slash",
	},
	showIcon: {
		type: String,
		default: "pi pi-eye",
	},
	disabled: {
		type: Boolean,
		default: false,
	},
	placeholder: {
		type: String,
		default: null,
	},
	required: {
		type: Boolean,
		required: false,
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
	panelId: {
		type: String,
		default: null,
	},
	panelClass: {
		type: String,
		default: null,
	},
	panelStyle: {
		type: null,
		default: null,
	},
	panelProps: {
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

const containerClass = computed(() => {
	return [
		"p-password p-component p-inputwrapper",
		{
			"p-inputwrapper-filled": filled.value,
			"p-inputwrapper-focus": focused,
			"p-input-icon-right": props.toggleMask,
		},
	];
});

const inputFieldClass = computed(() => {
	return [
		"p-password-input",
		props.inputClass,
		{
			"p-disabled": props.disabled,
		},
	];
});

const panelStyleClass = computed(() => {
	return [
		"p-password-panel p-component",
		props.panelClass,
		{
			"p-input-filled": $primevue.config.inputStyle === "filled",
			"p-ripple-disabled": $primevue.config.ripple === false,
		},
	];
});

const toggleIconClass = computed(() => {
	return unmasked.value ? props.hideIcon : props.showIcon;
});

const strengthClass = computed(() => {
	return `p-password-strength ${pMeter.value ? pMeter.value.strength : ""}`;
});

const inputType = computed(() => {
	return unmasked.value ? "text" : "password";
});

const filled = computed(() => {
	return props.modelValue != null && props.modelValue.toString().length > 0;
});

const weakText = computed(() => {
	return props.weakLabel || $primevue.config.locale.weak;
});

const mediumText = computed(() => {
	return props.mediumLabel || $primevue.config.locale.medium;
});

const strongText = computed(() => {
	return props.strongLabel || $primevue.config.locale.strong;
});

const promptText = computed(() => {
	return props.promptLabel || $primevue.config.locale.passwordPrompt;
});

const panelUniqueId = computed(() => {
	return UniqueComponentId() + "_panel";
});

onMounted(() => {
	infoText.value = promptText.value;
	mediumCheckRegExp = new RegExp(props.mediumRegex);
	strongCheckRegExp = new RegExp(props.strongRegex);
});

onBeforeUnmount(() => {
	unbindResizeListener();

	if (scrollHandler) {
		scrollHandler.destroy();
		scrollHandler = null;
	}

	if (overlay) {
		ZIndexUtils.clear(overlay);
		overlay = null;
	}
});

function onOverlayEnter(el) {
	ZIndexUtils.set("overlay", el, $primevue.config.zIndex.overlay);
	alignOverlay();
	bindScrollListener();
	bindResizeListener();
}

function onOverlayLeave() {
	unbindScrollListener();
	unbindResizeListener();
	overlay = null;
}

function onOverlayAfterLeave(el) {
	ZIndexUtils.clear(el);
}

function alignOverlay() {
	if (props.appendTo === "self") {
		DomHandler.relativePosition(overlay, input.value.$el);
	} else {
		overlay.style.minWidth = DomHandler.getOuterWidth(input.value.$el) + "px";
		DomHandler.absolutePosition(overlay, input.value.$el);
	}
}

async function testStrength(str) {
	let level = 0;

	scorePwd.value = await zxcvbnAsync(str);
	level = scorePwd.value.score;
	if (scorePwd.value.feedback?.warning) {
		scorePwdFeedback.value = scorePwd.value.feedback.warning;
		scorePwdSuggestions.value = scorePwd.value.feedback.suggestions;
	} else {
		scorePwdFeedback.value = null;
		scorePwdSuggestions.value = [];
	}

	// if (strongCheckRegExp.test(str)) level = 3;
	// else if (mediumCheckRegExp.test(str)) level = 2;
	// else if (str.length) level = 1;

	return level;
}

function onInput(event) {
	$emit("update:modelValue", event.target.value);
}

function onFocus(event) {
	focused.value = true;

	if (props.feedback) {
		setPasswordMeter(props.modelValue);
		overlayVisible.value = true;
	}

	$emit("focus", event);
}

function onBlur(event) {
	focused.value = false;

	if (props.feedback) {
		overlayVisible.value = false;
	}

	$emit("blur", event);
}

function onKeyUp(event) {
	if (props.feedback) {
		// const value = event.target.value;
		// const { meter, label } = checkPasswordStrength(value);

		// pMeter = meter;
		// infoText = label;

		if (event.code === "Escape") {
			overlayVisible.value && (overlayVisible.value = false);

			return;
		}

		if (!overlayVisible.value) {
			overlayVisible.value = true;
		}
	}
}

async function setPasswordMeter() {
	if (!props.modelValue) return;

	const { meter, label } = await checkPasswordStrength(props.modelValue);

	pMeter.value = meter;
	infoText.value = scorePwdFeedback.value ?? label.value;

	if (!overlayVisible.value) {
		overlayVisible.value = true;
	}
}

async function checkPasswordStrength(value) {
	let label = null;
	let meter = null;

	switch (await testStrength(value)) {
		case 0:
			label = weakText;
			meter = {
				strength: "weak",
				width: "0%",
			};
			break;

		case 1:
			label = weakText;
			meter = {
				strength: "weak",
				width: "25%",
			};
			break;

		case 2:
			label = mediumText;
			meter = {
				strength: "medium",
				width: "50%",
			};
			break;

		case 3:
			label = strongText;
			meter = {
				strength: "strong",
				width: "75%",
			};
			break;

		case 4:
			label = strongText;
			meter = {
				strength: "strong",
				width: "100%",
			};
			break;

		default:
			label = promptText;
			meter = null;
			scorePwd.value = null;
			break;
	}

	return { label, meter };
}

function onInvalid(event) {
	$emit("invalid", event);
}

function bindScrollListener() {
	if (!scrollHandler) {
		scrollHandler = new ConnectedOverlayScrollHandler(input.value.$el, () => {
			if (overlayVisible.value) {
				overlayVisible.value = false;
			}
		});
	}

	scrollHandler.bindScrollListener();
}

function unbindScrollListener() {
	if (scrollHandler) {
		scrollHandler.unbindScrollListener();
	}
}

function bindResizeListener() {
	if (!resizeListener) {
		resizeListener = () => {
			if (overlayVisible.value && !DomHandler.isTouchDevice()) {
				overlayVisible.value = false;
			}
		};

		window.addEventListener("resize", resizeListener);
	}
}

function unbindResizeListener() {
	if (resizeListener) {
		window.removeEventListener("resize", resizeListener);
		resizeListener = null;
	}
}

function overlayRef(el) {
	overlay = el;
}

function onMaskToggle() {
	unmasked.value = !unmasked.value;
}

function onOverlayClick(event) {
	OverlayEventBus.emit("overlay-click", {
		originalEvent: event,
		target: $el,
	});
}
</script>

<style>
.p-password {
	position: relative;
	display: inline-flex;
}

.p-password-panel {
	position: absolute;
	top: 0;
	left: 0;
}

.p-password .p-password-panel {
	min-width: 100%;
}

.p-password-meter {
	height: 10px;
}

.p-password-strength {
	height: 100%;
	width: 0;
	transition: width 1s ease-in-out;
}

.p-fluid .p-password {
	display: flex;
}
</style>
