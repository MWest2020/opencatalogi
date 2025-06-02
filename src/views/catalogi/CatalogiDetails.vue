<script setup>
import { navigationStore, objectStore } from '../../store/store.js'
</script>

<template>
	<div class="detailContainer">
		<div class="head">
			<h1 class="h1">
				{{ catalogi.title }}
			</h1>

			<NcActions :disabled="objectStore.isLoading('catalog')"
				:primary="true"
				:inline="1"
				:menu-name="objectStore.isLoading('catalog') ? 'Laden...' : 'Acties'">
				<template #icon>
					<span>
						<NcLoadingIcon v-if="objectStore.isLoading('catalog')"
							:size="20"
							appearance="dark" />
						<DotsHorizontal v-if="!objectStore.isLoading('catalog')" :size="20" />
					</span>
				</template>
				<NcActionButton
					title="Bekijk de documentatie over catalogi"
					@click="openLink('https://conduction.gitbook.io/opencatalogi-nextcloud/beheerders/catalogi', '_blank')">
					<template #icon>
						<HelpCircleOutline :size="20" />
					</template>
					Help
				</NcActionButton>
				<NcActionButton @click="navigationStore.setModal('catalog')">
					<template #icon>
						<Pencil :size="20" />
					</template>
					Bewerken
				</NcActionButton>
				<NcActionButton @click="navigationStore.setSelected('publication'); navigationStore.setSelectedCatalogus(catalogi?.id)">
					<template #icon>
						<OpenInApp :size="20" />
					</template>
					Catalogus bekijken
				</NcActionButton>
				<NcActionButton @click="navigationStore.setDialog('copyObject', { objectType: 'catalog', dialogTitle: 'Catalogus' })">
					<template #icon>
						<ContentCopy :size="20" />
					</template>
					Kopiëren
				</NcActionButton>
				<NcActionButton @click="navigationStore.setDialog('deleteObject', { objectType: 'catalog', dialogTitle: 'Catalogus' })">
					<template #icon>
						<Delete :size="20" />
					</template>
					Verwijderen
				</NcActionButton>
			</NcActions>
		</div>
		<div class="container">
			<div class="catalogDetailGrid">
				<div>
					<b>Samenvatting:</b>
					<span>{{ catalogi.summary }}</span>
				</div>
				<div class="catalogDetailGridOrganization">
					<b class="catalogDetailGridOrganizationTitle">Organisatie:</b>
					<span v-if="objectStore.isLoading('organization')">Loading...</span>

					<div v-if="!organization">
						Geen organisatie
					</div>
					<div v-if="organization">
						<div v-if="!objectStore.isLoading('organization')" class="buttonLinkContainer">
							<span>{{ organization?.name }}</span>
							<NcActions>
								<NcActionLink :aria-label="`go to ${organization?.name}`"
									:name="organization?.name"
									@click="goToOrganization()">
									<template #icon>
										<OpenInApp :size="20" />
									</template>
									{{ organization?.name }}
								</NcActionLink>
							</NcActions>
						</div>
					</div>
				</div>
				<div>
					<b>Beschrijving:</b>
					<span>{{ catalogi.description || '-' }}</span>
				</div>
			</div>
		</div>
		<div class="tabContainer">
			<BTabs content-class="mt-3" justified>
				<BTab title="Publicatietypes">
					<div v-if="catalogi.publicationTypes?.length > 0 && !objectStore.isLoading('publicationType')">
						<NcListItem v-for="(id, i) in catalogi.publicationTypes"
							:key="id + i"
							:name="filteredPublicationType(id)?.title || 'loading...'"
							:bold="false"
							:force-display-actions="true">
							<template #icon>
								<FileTreeOutline disable-menu
									:size="44" />
							</template>
							<template #subname>
								{{ filteredPublicationType(id)?.description }}
							</template>
							<template #actions>
								<NcActionButton @click="objectStore.setActiveObject('publicationType', filteredPublicationType(id)); navigationStore.setSelected('publicationType')">
									<template #icon>
										<OpenInApp :size="20" />
									</template>
									Bekijk publicatietype
								</NcActionButton>
								<NcActionButton @click="objectStore.setActiveObject('publicationType', filteredPublicationType(id)); navigationStore.setDialog('deleteCatalogiPublicationType')">
									<template #icon>
										<Delete :size="20" />
									</template>
									Verwijderen
								</NcActionButton>
							</template>
						</NcListItem>
					</div>
					<div v-if="!catalogi.publicationTypes?.length">
						<b class="emptyStateMessage">
							Geen publicatietypes gevonden
						</b>
					</div>
				</BTab>
				<BTab title="Toegang">
					<b class="emptyStateMessage">
						Publiek of alleen bepaalde rollen
					</b>
				</BTab>
			</BTabs>
		</div>
	</div>
</template>

<script>
import {
	NcActions,
	NcActionButton,
	NcLoadingIcon,
	NcListItem,
	NcActionLink,
} from '@nextcloud/vue'
import { BTabs, BTab } from 'bootstrap-vue'

import DotsHorizontal from 'vue-material-design-icons/DotsHorizontal.vue'
import Pencil from 'vue-material-design-icons/Pencil.vue'
import Delete from 'vue-material-design-icons/Delete.vue'
import OpenInApp from 'vue-material-design-icons/OpenInApp.vue'
import HelpCircleOutline from 'vue-material-design-icons/HelpCircleOutline.vue'
import FileTreeOutline from 'vue-material-design-icons/FileTreeOutline.vue'
import ContentCopy from 'vue-material-design-icons/ContentCopy.vue'

export default {
	name: 'CatalogiDetails',
	components: {
		NcActions,
		NcActionButton,
		NcLoadingIcon,
		NcListItem,
		NcActionLink,
		BTabs,
		BTab,
		DotsHorizontal,
		Pencil,
		Delete,
		OpenInApp,
		HelpCircleOutline,
		FileTreeOutline,
		ContentCopy,
	},
	computed: {
		catalogi() {
			return objectStore.getActiveObject('catalog')
		},
		organization() {
			return this.catalogi?.organization ? objectStore.getObject('organization', this.catalogi.organization) : null
		},
	},
	methods: {
		filteredPublicationType(id) {
			return objectStore.getObject('publicationType', id)
		},
		goToOrganization() {
			if (this.organization) {
				objectStore.setActiveObject('organization', this.organization)
				navigationStore.setSelected('organizations')
			}
		},
		openLink(url, type = '') {
			window.open(url, type)
		},
	},
}
</script>

<style>
h4 {
  font-weight: bold
}

.h1 {
  display: block !important;
  font-size: 2em !important;
  margin-block-start: 0.67em !important;
  margin-block-end: 0.67em !important;
  margin-inline-start: 0px !important;
  margin-inline-end: 0px !important;
  font-weight: bold !important;
  unicode-bidi: isolate !important;
}

.grid {
  display: grid;
  grid-gap: 24px;
  grid-template-columns: 1fr 1fr;
  margin-block-start: var(--OC-margin-50);
  margin-block-end: var(--OC-margin-50);
}

.gridContent {
  display: flex;
  gap: 25px;
}

.tabPanel {
  padding: 20px 10px;
  min-height: 100%;
  max-height: 100%;
  height: 100%;
  overflow: auto;
}

.flex-hor {
    display: flex;
    gap: 4px;
}

.buttonLinkContainer {
	display: flex;
	align-items: center;
}

.catalogDetailGrid {
	display: grid;
	grid-template-columns: 1fr 1fr;
}

.catalogDetailGridOrganization {
	display: flex;
    align-items: center;
}

.catalogDetailGridOrganizationTitle {
	margin-inline-end: 1ch;
}

.emptyStateMessage {
    margin-block-start: 15px;
    text-align: center;
    display: flex;
    justify-content: center;
}
</style>
