/**
 * EditMenuModal.vue
 * Modal for editing menus
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
</script>

<template>
	<NcModal v-if="navigationStore.modal === 'editMenu'"
		ref="modalRef"
		class="editMenuModal"
		label-id="editMenuModal"
		@close="closeModal">
		<div class="modal__content">
			<h2>Menu bewerken</h2>
			<div v-if="success !== null || error">
				<NcNoteCard v-if="success" type="success">
					<p>Menu succesvol bewerkt</p>
				</NcNoteCard>
				<NcNoteCard v-if="!success" type="error">
					<p>Er is iets fout gegaan bij het bewerken van menu</p>
				</NcNoteCard>
				<NcNoteCard v-if="error" type="error">
					<p>{{ error }}</p>
				</NcNoteCard>
			</div>
			<div v-if="success === null" class="form-group">
				<NcTextField
					v-model="menu.title"
					label="Titel"
					:disabled="loading"
					:loading="loading" />
				<NcTextField
					v-model="menu.description"
					label="Beschrijving"
					:disabled="loading"
					:loading="loading" />
				<NcCheckboxRadioSwitch
					v-model="menu.published"
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
							<Save v-if="!loading" :size="20" />
						</span>
					</template>
					Opslaan
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
	NcCheckboxRadioSwitch,
	NcNoteCard,
	NcLoadingIcon,
} from '@nextcloud/vue'

// icons
import Save from 'vue-material-design-icons/ContentSave.vue'

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
 * Get the active menu from the store
 * @return {object | null}
 */
const menu = computed(() => objectStore.getActiveObject('menu'))

/**
 * Handle save action
 * @return {Promise<void>}
 */
const handleSave = async () => {
	loading.value = true
	try {
		await objectStore.updateObject('menu', menu.value)
		success.value = true
	} catch (error) {
		console.error('Error saving menu:', error)
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
	name: 'EditMenuModal',
	components: {
		NcModal,
		NcTextField,
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
