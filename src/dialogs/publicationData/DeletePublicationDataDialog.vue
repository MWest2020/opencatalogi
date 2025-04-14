<script setup>
import { navigationStore, objectStore } from '../../store/store.js'
</script>

<template>
	<NcDialog
		v-if="navigationStore.dialog === 'deletePublicationData'"
		name="Publicatie gegevens verwijderen"
		:can-close="false">
		<div v-if="objectStore.getState('publicationData').success !== null || objectStore.getState('publicationData').error">
			<NcNoteCard v-if="objectStore.getState('publicationData').success" type="success">
				<p>Publicatie gegevens succesvol verwijderd</p>
			</NcNoteCard>
			<NcNoteCard v-if="!objectStore.getState('publicationData').success" type="error">
				<p>Er is iets fout gegaan bij het verwijderen van publicatie gegevens</p>
			</NcNoteCard>
			<NcNoteCard v-if="objectStore.getState('publicationData').error" type="error">
				<p>{{ objectStore.getState('publicationData').error }}</p>
			</NcNoteCard>
		</div>
		<div v-if="objectStore.isLoading('publicationData')" class="loading-status">
			<NcLoadingIcon :size="20" />
			<span>Publicatie gegevens wordt verwijderd...</span>
		</div>
		<p v-if="objectStore.getState('publicationData').success === null && !objectStore.isLoading('publicationData')">
			Wil je <b>{{ objectStore.getActiveObject('publicationData')?.title }}</b> definitief verwijderen? Deze actie kan niet ongedaan worden gemaakt.
		</p>
		<template v-if="objectStore.getState('publicationData').success === null && !objectStore.isLoading('publicationData')" #actions>
			<NcButton 
				:disabled="objectStore.isLoading('publicationData')" 
				icon="" 
				@click="navigationStore.setDialog(false)">
				<template #icon>
					<Cancel :size="20" />
				</template>
				Annuleer
			</NcButton>
			<NcButton
				:disabled="objectStore.isLoading('publicationData')"
				icon="Delete"
				type="error"
				@click="deletePublicationData()">
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

<script>
import { NcButton, NcDialog, NcNoteCard, NcLoadingIcon } from '@nextcloud/vue'

import Cancel from 'vue-material-design-icons/Cancel.vue'
import Delete from 'vue-material-design-icons/Delete.vue'

/**
 * Delete publication data dialog component
 *
 * @category Dialogs
 * @package
 * @author Your Name
 * @copyright 2024
 * @license MIT
 * @version 1.0.0
 * @link https://github.com/your-repo
 */
export default {
	name: 'DeletePublicationDataDialog',
	components: {
		NcDialog,
		NcButton,
		NcNoteCard,
		NcLoadingIcon,
		// Icons
		Cancel,
		Delete,
	},
	methods: {
		/**
		 * Delete the active publication data
		 *
		 * @return {void}
		 */
		deletePublicationData() {
			const activePublicationData = objectStore.getActiveObject('publicationData')
			if (!activePublicationData?.id) return

			objectStore.deleteObject('publicationData', activePublicationData.id)
				.then(() => {
					// Wait for the user to read the feedback then close the dialog
					setTimeout(() => {
						objectStore.setState('publicationData', { success: null, error: null })
						navigationStore.setDialog(false)
					}, 2000)
				})
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

.loading-status {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    margin: 1rem 0;
    color: var(--color-text-lighter);
}
</style>
