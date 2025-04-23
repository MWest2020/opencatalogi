<script setup>
import { navigationStore, catalogStore } from '../../store/store.js'
</script>

<template>
	<div class="detailContainer">
		<div class="head">
			<h1 class="h1">
				{{ catalogStore.getActivePublication?.title }}
			</h1>

			<NcActions :disabled="catalogStore.isLoading"
				:primary="true"
				:menu-name="catalogStore.isLoading ? 'Laden...' : 'Acties'"
				:inline="1"
				title="Acties die je kan uitvoeren op deze publicatie">
				<template #icon>
					<span>
						<NcLoadingIcon v-if="catalogStore.isLoading" :size="20" appearance="dark" />
						<DotsHorizontal v-if="!catalogStore.isLoading" :size="20" />
					</span>
				</template>
				<NcActionButton title="Bekijk de documentatie over publicaties"
					@click="openLink('https://conduction.gitbook.io/opencatalogi-nextcloud/gebruikers/publicaties', '_blank')">
					<template #icon>
						<HelpCircleOutline :size="20" />
					</template>
					Help
				</NcActionButton>
				<NcActionButton @click="navigationStore.setModal('editPublication')">
					<template #icon>
						<Pencil :size="20" />
					</template>
					Bewerken
				</NcActionButton>
				<NcActionButton @click="navigationStore.setDialog('copyPublication')">
					<template #icon>
						<ContentCopy :size="20" />
					</template>
					Kopiëren
				</NcActionButton>
				<NcActionButton v-if="catalogStore.getActivePublication?.status !== 'Published'"
					@click="catalogStore.setActivePublication(catalogStore.getActivePublication); navigationStore.setDialog('publishPublication')">
					<template #icon>
						<Publish :size="20" />
					</template>
					Publiceren
				</NcActionButton>
				<NcActionButton v-if="catalogStore.getActivePublication?.status === 'Published'"
					@click="catalogStore.setActivePublication(catalogStore.getActivePublication); navigationStore.setDialog('depublishPublication')">
					<template #icon>
						<PublishOff :size="20" />
					</template>
					Depubliceren
				</NcActionButton>
				<NcActionButton @click="navigationStore.setDialog('archivePublication')">
					<template #icon>
						<ArchivePlusOutline :size="20" />
					</template>
					Archiveren
				</NcActionButton>
				<NcActionButton @click="navigationStore.setDialog('downloadPublication')">
					<template #icon>
						<Download :size="20" />
					</template>
					Downloaden
				</NcActionButton>
				<NcActionButton @click="navigationStore.setModal('addPublicationData')">
					<template #icon>
						<FileTreeOutline :size="20" />
					</template>
					Eigenschap toevoegen
				</NcActionButton>
				<NcActionButton disabled>
					<template #icon>
						<FolderOutline :size="20" />
					</template>
					Bijlage toevoegen
				</NcActionButton>
				<NcActionButton @click="navigationStore.setModal('addPublicationTheme')">
					<template #icon>
						<ShapeOutline :size="20" />
					</template>
					Thema toevoegen
				</NcActionButton>
				<NcActionButton @click="navigationStore.setDialog('deletePublication')">
					<template #icon>
						<Delete :size="20" />
					</template>
					Verwijderen
				</NcActionButton>
			</NcActions>
		</div>
		<div class="container">
			<div class="detailGrid">
				<div v-if="publication.reference">
					<b>Referentie:</b>
					<span>{{ publication.reference }}</span>
				</div>
				<div v-if="publication.summary">
					<b>Samenvatting:</b>
					<span>{{ publication.summary || '-' }}</span>
				</div>
				<div v-if="publication.description">
					<b>Beschrijving:</b>
					<span>{{ publication.description || '-' }}</span>
				</div>
				<div v-if="publication.category">
					<b>Categorie:</b>
					<span>{{ publication.category || '-' }}</span>
				</div>
				<div v-if="publication.portal">
					<b>Portal:</b>
					<span><a target="_blank" :href="publication.portal">{{
						publication.portal || '-' }}</a></span>
				</div>
				<div v-if="publication.image">
					<b>Afbeelding:</b>
					<span>{{ publication.image || '-' }}</span>
				</div>
				<div v-if="publication.featured">
					<b>Uitgelicht:</b>
					<span>{{ publication.featured ? "Ja" : "Nee" }}</span>
				</div>
				<div v-if="publication.license">
					<b>Licentie:</b>
					<span>{{ publication.license || '-' }}</span>
				</div>
				<div v-if="publication.status">
					<b>Status:</b>
					<span>{{ publication.status || '-' }}</span>
				</div>
				<div v-if="publication.published">
					<b>Gepubliceerd:</b>
					<span>{{ new Date(publication.published).toLocaleDateString() || '-' }}</span>
				</div>
				<div v-if="publication.modified">
					<b>Gewijzigd:</b>
					<span>{{ publication.modified || '-' }}</span>
				</div>
				<div v-if="publication.source">
					<b>Bron:</b>
					<span>{{ publication.source || '-' }}</span>
				</div>
				<div v-if="publication.catalogi">
					<b>Catalogi:</b>
					<span v-if="catalogiLoading">Loading...</span>
					<div v-if="!catalogiLoading" class="buttonLinkContainer">
						<span>{{ catalogi?.title }}</span>
						<NcActions>
							<NcActionLink :aria-label="`ga naar ${catalogi?.title}`"
								:name="catalogi?.title"
								@click="goToCatalogi()">
								<template #icon>
									<OpenInApp :size="20" />
								</template>
								{{ catalogi?.title }}
							</NcActionLink>
						</NcActions>
					</div>
				</div>
			</div>
			<div class="detailGrid linksParameters">
				<div>
					<b>Organisatie:</b>
					<span v-if="organizationLoading">Loading...</span>
					<div v-if="!organizationLoading && organization?.title" class="buttonLinkContainer">
						<span>{{ organization?.title }}</span>
						<NcActions>
							<NcActionLink :aria-label="`ga naar ${organization?.title}`"
								:name="organization?.title"
								@click="goToOrganization()">
								<template #icon>
									<OpenInApp :size="20" />
								</template>
								{{ organization?.title }}
							</NcActionLink>
						</NcActions>
					</div>
					<div v-if="!organizationLoading && !organization?.title" class="buttonLinkContainer">
						<span>Geen organisatie gekoppeld</span>
					</div>
				</div>
			</div>
		</div>
	</div>
</template>

<script>
import { NcActionButton, NcActions, NcLoadingIcon } from '@nextcloud/vue'

// Icons
import ArchivePlusOutline from 'vue-material-design-icons/ArchivePlusOutline.vue'
import ContentCopy from 'vue-material-design-icons/ContentCopy.vue'
import Delete from 'vue-material-design-icons/Delete.vue'
import DotsHorizontal from 'vue-material-design-icons/DotsHorizontal.vue'
import Download from 'vue-material-design-icons/Download.vue'
import FileTreeOutline from 'vue-material-design-icons/FileTreeOutline.vue'
import HelpCircleOutline from 'vue-material-design-icons/HelpCircleOutline.vue'
import Pencil from 'vue-material-design-icons/Pencil.vue'
import Publish from 'vue-material-design-icons/Publish.vue'
import PublishOff from 'vue-material-design-icons/PublishOff.vue'
import FolderOutline from 'vue-material-design-icons/FolderOutline.vue'
import ShapeOutline from 'vue-material-design-icons/ShapeOutline.vue'

export default {
	name: 'PublicationDetail',
	components: {
		NcActionButton,
		NcActions,
		NcLoadingIcon,
	},
	data() {
		return {
			test: false,
		}
	},
	computed: {
		publication() {
			return catalogStore.getActivePublication
		},
	},
	methods: {
		openLink(url, type = '') {
			window.open(url, type)
		},
	},
}
</script>

<style scoped>
.detailContainer {
	display: flex;
	flex-direction: column;
	gap: 1rem;
}
</style>
