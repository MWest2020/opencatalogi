/**
 * AddPageContents.vue
 * Modal for adding page contents
 * @category Components
 * @package opencatalogi
 * @author Ruben Linde
 * @copyright 2024
 * @license AGPL-3.0-or-later
 * @version 1.0.0
 * @link https://github.com/opencatalogi/opencatalogi
 */

<script setup>
import { ref, computed } from 'vue'
import { objectStore, navigationStore } from '../../store/store.js'
import { getTheme } from '../../services/getTheme.js'
import { EventBus } from '../../eventBus.js'
</script>

<template>
	<NcModal v-if="navigationStore.modal === 'addPageContent'"
		ref="modalRef"
		class="addPageContentModal"
		label-id="addPageContentModal"
		@close="closeModal">
		<div class="modal__content">
			<h2>Pagina inhoud toevoegen</h2>
			<div v-if="success !== null || error">
				<NcNoteCard v-if="success" type="success">
					<p>Pagina inhoud succesvol toegevoegd</p>
				</NcNoteCard>
				<NcNoteCard v-if="!success" type="error">
					<p>Er is iets fout gegaan bij het toevoegen van pagina inhoud</p>
				</NcNoteCard>
				<NcNoteCard v-if="error" type="error">
					<p>{{ error }}</p>
				</NcNoteCard>
			</div>
			<div v-if="success === null" class="form-group">
				<NcTextField
					v-model="newPageContent.title"
					label="Titel"
					:disabled="loading"
					:loading="loading" />
				<NcTextArea
					v-model="newPageContent.content"
					label="Inhoud"
					:disabled="loading"
					:loading="loading" />
				<NcCheckboxRadioSwitch
					v-model="newPageContent.published"
					:disabled="loading"
					:loading="loading">
					Gepubliceerd
				</NcCheckboxRadioSwitch>
			</div>

			<span class="buttonContainer">
				<NcButton
					@click="navigationStore.setModal(false)">
					{{ success ? 'Sluiten' : 'Annuleer' }}
				</NcButton>
				<NcButton v-if="success === null"
					:disabled="loading"
					type="primary"
					@click="handleSave">
					<template #icon>
						<span>
							<NcLoadingIcon v-if="loading" :size="20" />
							<Plus v-if="!loading" :size="20" />
						</span>
					</template>
					Toevoegen
				</NcButton>
			</span>
		</div>
	</NcModal>
</template>

<script>
import {
	NcButton,
	NcModal,
	NcTextField,
	NcTextArea,
	NcCheckboxRadioSwitch,
	NcNoteCard,
	NcLoadingIcon,
} from '@nextcloud/vue'

// icons
import Plus from 'vue-material-design-icons/Plus.vue'

/**
 * Loading state for the component
 * @type {import('vue').Ref<boolean>}
 */
const loading = ref(false)

/**
 * Success state for the component
 * @type {import('vue').Ref<boolean|null>}
 */
const success = ref(null)

/**
 * Error state for the component
 * @type {import('vue').Ref<string|null>}
 */
const error = ref(null)

/**
 * Get the active page from the store
 * @return {object | null}
 */
const page = computed(() => objectStore.getActiveObject('page'))

/**
 * New page content data
 * @type {import('vue').Ref<object>}
 */
const newPageContent = ref({
	title: '',
	content: '',
	published: false,
})

/**
 * Handle save action
 * @return {Promise<void>}
 */
const handleSave = async () => {
	loading.value = true
	try {
		await objectStore.createObject('pageContent', {
			...newPageContent.value,
			page: page.value.id,
		})
		success.value = true
	} catch (error) {
		console.error('Error saving page content:', error)
		success.value = false
		error.value = error.message
	} finally {
		loading.value = false
	}
}

/**
 * Handle cancel action
 * @return {void}
 */
const handleCancel = () => {
	navigationStore.setModal(false)
}

export default {
	name: 'AddPageContents',
	components: {
		NcModal,
		NcTextField,
		NcTextArea,
		NcCheckboxRadioSwitch,
		NcButton,
		NcNoteCard,
		NcLoadingIcon,
	},
	data() {
		return {
			loading: false,
			success: null,
			error: null,
		}
	},
	methods: {
		closeModal() {
			this.navigationStore.setModal(false)
		},
	},
}
</script>

<style>
.modal__content {
    margin: var(--OC-margin-50);
    text-align: center;
}

.zaakDetailsContainer {
    margin-block-start: var(--OC-margin-20);
    margin-inline-start: var(--OC-margin-20);
    margin-inline-end: var(--OC-margin-20);
}

.success {
    color: green;
}
</style>

<style scoped>
.modal__content {
	padding: 20px;
}

.buttonContainer {
	display: flex;
	justify-content: flex-end;
	gap: 10px;
	margin-top: 20px;
}

.form-group {
	display: flex;
	flex-direction: column;
	gap: 10px;
	margin-top: 20px;
}
</style>
