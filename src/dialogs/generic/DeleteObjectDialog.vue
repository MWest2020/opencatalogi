<script setup>
import { NcButton, NcDialog, NcNoteCard, NcLoadingIcon } from '@nextcloud/vue'
import Cancel from 'vue-material-design-icons/Cancel.vue'
import Delete from 'vue-material-design-icons/Delete.vue'
import { navigationStore, objectStore } from '../../store/store.js'
import { computed } from 'vue'

const dialogProperties = computed(() => navigationStore.dialogProperties)

const objectType = computed(() => dialogProperties.value?.objectType)
const dialogName = computed(() => dialogProperties.value?.dialogName)
const displayName = computed(() => dialogProperties.value?.displayName)
const isMultiple = computed(() => dialogProperties.value?.isMultiple ?? false)

// Check if this dialog should be shown
const shouldShowDialog = computed(() => navigationStore.dialog === 'deleteCatalog')

/**
 * Delete the object(s)
 *
 * @return {void}
 */
const deleteObject = () => {
	if (isMultiple.value) {
		const selectedObjects = objectStore.getSelectedObjects(objectType.value)
		if (!selectedObjects?.length) return

		Promise.all(selectedObjects.map(obj =>
			objectStore.deleteObject(objectType.value, obj.id),
		))
			.then(() => {
				closeDialog()
			})
	} else {
		const activeObject = objectStore.getActiveObject(objectType.value)
		if (!activeObject?.id) return

		objectStore.deleteObject(objectType.value, activeObject.id)
			.then(() => {
				closeDialog()
			})
	}
}

/**
 * Close the dialog after a delay
 *
 * @return {void}
 */
const closeDialog = () => {
	setTimeout(() => {
		objectStore.setState(objectType.value, { success: null, error: null })
		navigationStore.setDialog(false)
	}, 2000)
}
</script>

<template>
	<NcDialog
		v-if="shouldShowDialog"
		:name="`${displayName}${isMultiple ? 's' : ''} verwijderen`"
		:can-close="false">
		<div v-if="objectStore.getState(objectType).success !== null || objectStore.getState(objectType).error">
			<NcNoteCard v-if="objectStore.getState(objectType).success" type="success">
				<p>{{ displayName }}{{ isMultiple ? 's' : '' }} succesvol verwijderd</p>
			</NcNoteCard>
			<NcNoteCard v-if="!objectStore.getState(objectType).success" type="error">
				<p>Er is iets fout gegaan bij het verwijderen van {{ displayName.toLowerCase() }}{{ isMultiple ? 's' : '' }}</p>
			</NcNoteCard>
			<NcNoteCard v-if="objectStore.getState(objectType).error" type="error">
				<p>{{ objectStore.getState(objectType).error }}</p>
			</NcNoteCard>
		</div>
		<div v-if="objectStore.isLoading(objectType)" class="loading-status">
			<NcLoadingIcon :size="20" />
			<span>{{ displayName }}{{ isMultiple ? 's' : '' }} {{ isMultiple ? 'worden' : 'wordt' }} verwijderd...</span>
		</div>
		<p v-if="objectStore.getState(objectType).success === null && !objectStore.isLoading(objectType)">
			<template v-if="isMultiple">
				Wil je de geselecteerde {{ displayName.toLowerCase() }}s definitief verwijderen? Deze actie kan niet ongedaan worden gemaakt.
			</template>
			<template v-else>
				Wil je <b>{{ objectStore.getActiveObject(objectType)?.name || objectStore.getActiveObject(objectType)?.title }}</b> definitief verwijderen? Deze actie kan niet ongedaan worden gemaakt.
			</template>
		</p>
		<template v-if="objectStore.getState(objectType).success === null && !objectStore.isLoading(objectType)" #actions>
			<NcButton
				:disabled="objectStore.isLoading(objectType)"
				icon=""
				@click="navigationStore.setDialog(false)">
				<template #icon>
					<Cancel :size="20" />
				</template>
				Annuleer
			</NcButton>
			<NcButton
				:disabled="objectStore.isLoading(objectType)"
				icon="Delete"
				type="error"
				@click="deleteObject">
				<template #icon>
					<Delete :size="20" />
				</template>
				Verwijderen
			</NcButton>
		</template>
		<template v-else #actions>
			<NcButton
				icon=""
				@click="navigationStore.setDialog(false)">
				<template #icon>
					<Cancel :size="20" />
				</template>
				Sluiten
			</NcButton>
		</template>
	</NcDialog>
</template>

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

.loading-status {
	display: flex;
	align-items: center;
	justify-content: center;
	gap: 0.5rem;
	margin: 1rem 0;
	color: var(--color-text-lighter);
}
</style>
