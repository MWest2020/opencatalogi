<script setup>
import { navigationStore, objectStore } from '../../store/store.js'
</script>

<template>
	<NcDialog
		v-if="navigationStore.dialog === 'deleteOrganization'"
		name="Organisatie verwijderen"
		:can-close="false">
		<div v-if="objectStore.getState('organization').success !== null || objectStore.getState('organization').error">
			<NcNoteCard v-if="objectStore.getState('organization').success" type="success">
				<p>Organisatie succesvol verwijderd</p>
			</NcNoteCard>
			<NcNoteCard v-if="!objectStore.getState('organization').success" type="error">
				<p>Er is iets fout gegaan bij het verwijderen van Organisatie</p>
			</NcNoteCard>
			<NcNoteCard v-if="objectStore.getState('organization').error" type="error">
				<p>{{ objectStore.getState('organization').error }}</p>
			</NcNoteCard>
		</div>
		<p v-if="objectStore.getState('organization').success === null">
			Wil je <b>{{ objectStore.getActiveObject('organization')?.name }}</b> definitief verwijderen? Deze actie kan niet ongedaan worden gemaakt.
		</p>
		<template #actions>
			<NcButton :disabled="objectStore.isLoading('organization')" icon="" @click="navigationStore.setDialog(false)">
				<template #icon>
					<Cancel :size="20" />
				</template>
				{{ objectStore.getState('organization').success !== null ? 'Sluiten' : 'Annuleer' }}
			</NcButton>
			<NcButton
				v-if="objectStore.getState('organization').success === null"
				:disabled="objectStore.isLoading('organization')"
				icon="Delete"
				type="error"
				@click="deleteOrganization()">
				<template #icon>
					<NcLoadingIcon v-if="objectStore.isLoading('organization')" :size="20" />
					<Delete v-if="!objectStore.isLoading('organization')" :size="20" />
				</template>
				Verwijderen
			</NcButton>
		</template>
	</NcDialog>
</template>

<script>
import { NcButton, NcDialog, NcNoteCard, NcLoadingIcon } from '@nextcloud/vue'

import Cancel from 'vue-material-design-icons/Cancel.vue'
import Delete from 'vue-material-design-icons/Delete.vue'

/**
 * Delete organization dialog component
 * 
 * @category Dialogs
 * @package OpenCatalogi
 * @author Your Name
 * @copyright 2024
 * @license MIT
 * @version 1.0.0
 * @link https://github.com/your-repo
 */
export default {
	name: 'DeleteOrganizationDialog',
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
		 * Delete the active organization
		 * 
		 * @returns {void}
		 */
		deleteOrganization() {
			const activeOrganization = objectStore.getActiveObject('organization')
			if (!activeOrganization?.id) return

			objectStore.deleteObject('organization', activeOrganization.id)
				.then(() => {
					// Wait for the user to read the feedback then close the dialog
					setTimeout(() => {
						objectStore.setState('organization', { success: null, error: null })
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
</style>
