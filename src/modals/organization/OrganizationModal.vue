<script setup>
import { navigationStore, objectStore } from '../../store/store.js'
import { NcButton, NcModal, NcTextField, NcLoadingIcon, NcNoteCard, NcTextArea } from '@nextcloud/vue'
import Plus from 'vue-material-design-icons/Plus.vue'
import { Organization } from '../../entities/index.js'
import { ref, computed } from 'vue'

// Form data
const formData = ref({
	title: '',
	summary: '',
	description: '',
	oin: '',
	tooi: '',
	rsin: '',
	pki: '',
	image: '',
})

// Computed properties
const isEdit = computed(() => !!objectStore.getActiveObject('organization'))
const inputValidation = computed(() => {
	const organizationItem = new Organization({
		...formData.value,
	})
	const result = organizationItem.validate()
	return {
		success: result.success,
		errorMessages: result?.error?.issues.map(issue => `${issue.path.join('.')}: ${issue.message}`) || [],
		fieldErrors: result?.error?.formErrors?.fieldErrors || {},
	}
})

// Methods
const closeModal = () => {
	navigationStore.setModal(false)
	formData.value = {
		title: '',
		summary: '',
		description: '',
		oin: '',
		tooi: '',
		rsin: '',
		pki: '',
		image: '',
	}
	objectStore.setState('organization', { success: null, error: null })
}

const saveOrganization = () => {
	const organizationItem = new Organization({
		...formData.value,
	})

	const operation = isEdit.value
		? objectStore.updateObject('organization', organizationItem.id, organizationItem)
		: objectStore.createObject('organization', organizationItem)

	operation.then(() => {
		setTimeout(closeModal, 2000)
	})
}

// Initialize form if editing
if (isEdit.value) {
	const activeOrganization = objectStore.getActiveObject('organization')
	formData.value = { ...activeOrganization }
}
</script>

<template>
	<NcModal v-if="navigationStore.modal === 'organization'"
		ref="modalRef"
		:label-id="isEdit ? 'editOrganizationModal' : 'addOrganizationModal'"
		@close="closeModal">
		<div class="modal__content">
			<h2>Organisatie {{ isEdit ? 'bewerken' : 'toevoegen' }}</h2>
			<div v-if="objectStore.getState('organization').success !== null || objectStore.getState('organization').error">
				<NcNoteCard v-if="objectStore.getState('organization').success" type="success">
					<p>{{ isEdit ? 'Organisatie succesvol bewerkt' : 'Organisatie succesvol toegevoegd' }}</p>
				</NcNoteCard>
				<NcNoteCard v-if="!objectStore.getState('organization').success" type="error">
					<p>{{ isEdit ? 'Er is iets fout gegaan bij het bewerken van de organisatie' : 'Er is iets fout gegaan bij het toevoegen van organisatie' }}</p>
				</NcNoteCard>
				<NcNoteCard v-if="objectStore.getState('organization').error" type="error">
					<p>{{ objectStore.getState('organization').error }}</p>
				</NcNoteCard>
			</div>
			<div v-if="objectStore.getState('organization').success === null" class="formContainer">
				<NcTextField
					:disabled="objectStore.isLoading('organization')"
					label="Titel"
					:value.sync="formData.title"
					:error="!!inputValidation.fieldErrors?.['title']"
					:helper-text="inputValidation.fieldErrors?.['title']?.[0]" />
				<NcTextField
					:disabled="objectStore.isLoading('organization')"
					label="Samenvatting"
					:value.sync="formData.summary"
					:error="!!inputValidation.fieldErrors?.['summary']"
					:helper-text="inputValidation.fieldErrors?.['summary']?.[0]" />
				<NcTextArea
					:disabled="objectStore.isLoading('organization')"
					label="Beschrijving"
					:value.sync="formData.description"
					:error="!!inputValidation.fieldErrors?.['description']"
					:helper-text="inputValidation.fieldErrors?.['description']?.[0]"
					resize="vertical" />
				<NcTextField
					:disabled="objectStore.isLoading('organization')"
					label="OIN (organisatie-identificatienummer)"
					:value.sync="formData.oin"
					:error="!!inputValidation.fieldErrors?.['oin']"
					:helper-text="inputValidation.fieldErrors?.['oin']?.[0]" />
				<NcTextField
					:disabled="objectStore.isLoading('organization')"
					label="TOOI"
					:value.sync="formData.tooi"
					:error="!!inputValidation.fieldErrors?.['tooi']"
					:helper-text="inputValidation.fieldErrors?.['tooi']?.[0]" />
				<NcTextField
					:disabled="objectStore.isLoading('organization')"
					label="RSIN"
					:value.sync="formData.rsin"
					:error="!!inputValidation.fieldErrors?.['rsin']"
					:helper-text="inputValidation.fieldErrors?.['rsin']?.[0]" />
				<NcTextField
					:disabled="objectStore.isLoading('organization')"
					label="PKI"
					:value.sync="formData.pki"
					:error="!!inputValidation.fieldErrors?.['pki']"
					:helper-text="inputValidation.fieldErrors?.['pki']?.[0]" />
				<NcTextField
					:disabled="objectStore.isLoading('organization')"
					label="Afbeelding (url)"
					:value.sync="formData.image"
					:error="!!inputValidation.fieldErrors?.['image']"
					:helper-text="inputValidation.fieldErrors?.['image']?.[0]" />
			</div>
			<NcButton v-if="objectStore.getState('organization').success === null"
				v-tooltip="inputValidation.errorMessages?.[0]"
				:disabled="!inputValidation.success || objectStore.isLoading('organization')"
				type="primary"
				@click="saveOrganization">
				<template #icon>
					<NcLoadingIcon v-if="objectStore.isLoading('organization')" :size="20" />
					<Plus v-if="!objectStore.isLoading('organization')" :size="20" />
				</template>
				{{ isEdit ? 'Bewerken' : 'Toevoegen' }}
			</NcButton>
		</div>
	</NcModal>
</template>

<style>
.modal__content {
    margin: var(--OC-margin-50);
    text-align: center;
}

.formContainer > * {
    margin-block-end: 10px;
}

.selectGrid {
    display: grid;
    grid-gap: 5px;
    grid-template-columns: 1fr 1fr;
}

.zaakDetailsContainer {
    margin-block-start: var(--OC-margin-20);
    margin-inline-start: var(--OC-margin-20);
    margin-inline-end: var(--OC-margin-20);
}

.success {
    color: green;
}

.APM-horizontal {
    display: flex;
    gap: 4px;
    flex-direction: row;
    align-items: center;
}
</style>

<style scoped>
.formContainer {
    display: flex;
    flex-direction: column;
}
</style>
