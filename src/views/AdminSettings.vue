<script setup>
import { publicationStore } from '../store/store.js'
</script>

<template>
	<div>
		<NcSettingsSection :name="'Open Catalogi'" description="Eén centrale plek voor hergebruik van informatietechnologie binnen de overheid" doc-url="https://conduction.gitbook.io/opencatalogi-nextcloud/gebruikers" />
		<NcSettingsSection :name="'Data storage'" description="Korte uitleg over dat je kan opslaan in de nextcloud database of open registers en via open registers ook in extrene oplsag zo al mongo db">
			<NcNoteCard v-if="!openRegisterInstalled" type="info">
				Je hebt nog geen Open Registers ginstaleerd, we raden je aan om dat wel te doen. Dat kan via  <a href="/settings/apps/organization/openregister">deze link</a>
			</NcNoteCard>

			<NcSelect v-bind="publications"
				v-model="publications.value"
				input-label="Publicatie"
				:loading="publicationsLoading"
				:disabled="loading" />
		</NcSettingsSection>
	</div>
</template>

<script>
// Components
import { NcSettingsSection, NcNoteCard, NcSelect } from '@nextcloud/vue'

export default {
	name: 'AdminSettings',
	components: {
		NcSettingsSection,
		NcNoteCard,
		NcSelect,
	},
	data() {
		return {
			loading: false,
			openRegisterInstalled: false,
			publicationsLoading: false,
			publications: {},

		}
	},
	mounted() {
		this.fetchPublications()
	},

	methods: {
		fetchPublications() {
			this.publicationsLoading = true

			publicationStore.refreshPublicationList()
				.then((response) => {

					this.publications = {
						options: response.results.map((publication) => ({
							id: publication.id,
							label: publication.title,
						})),
					}

					this.publicationsLoading = false
				})
				.catch((err) => {
					console.error(err)
					this.publicationsLoading = false
				})
		},

	},

}
</script>
