<script setup>
import { navigationStore, objectStore } from '../../store/store.js'
</script>

<template>
	<NcDialog
		v-if="navigationStore.dialog === 'copyOrganization'"
		name="Organisatie kopieren"
		:can-close="false">
		<div v-if="objectStore.getState('organization').success !== null || objectStore.getState('organization').error">
			<NcNoteCard v-if="objectStore.getState('organization').success" type="success">
				<p>Organisatie succesvol gekopieerd</p>
			</NcNoteCard>
			<NcNoteCard v-if="!objectStore.getState('organization').success" type="error">
				<p>Er is iets fout gegaan bij het kopiëren van Organisatie</p>
			</NcNoteCard>
			<NcNoteCard v-if="objectStore.getState('organization').error" type="error">
				<p>{{ objectStore.getState('organization').error }}</p>
			</NcNoteCard>
		</div>
		<p v-if="objectStore.getState('organization').success === null">
			Wil je <b>{{ objectStore.getActiveObject('organization')?.name }}</b> kopiëren?
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
				type="primary"
				@click="copyOrganization()">
				<template #icon>
					<NcLoadingIcon v-if="objectStore.isLoading('organization')" :size="20" />
					<ContentCopy v-if="!objectStore.isLoading('organization')" :size="20" />
				</template>
				Kopiëren
			</NcButton>
		</template>
	</NcDialog>
</template>

<script>
import { NcButton, NcDialog, NcLoadingIcon, NcNoteCard } from '@nextcloud/vue'

import Cancel from 'vue-material-design-icons/Cancel.vue'
import ContentCopy from 'vue-material-design-icons/ContentCopy.vue'
import { Organization } from '../../entities/index.js'

/**
 * Copy organization dialog component
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
	name: 'CopyOrganizationDialog',
	components: {
		NcDialog,
		NcButton,
		NcNoteCard,
		NcLoadingIcon,
		// Icons
		Cancel,
		ContentCopy,
	},
	methods: {
		/**
		 * Copy the active organization
		 * 
		 * @returns {void}
		 */
		copyOrganization() {
			const activeOrganization = objectStore.getActiveObject('organization')
			if (!activeOrganization) return

			const organizationClone = { ...activeOrganization }
			organizationClone.name = 'KOPIE: ' + organizationClone.name
			delete organizationClone.id
			delete organizationClone._id

			const organizationItem = new Organization(organizationClone)

			objectStore.createObject('organization', organizationItem)
				.then(() => {
					navigationStore.setSelected('organizations')
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
