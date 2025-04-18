<script setup>
import { navigationStore, objectStore } from '../../store/store.js'
</script>

<template>
	<div class="detailContainer">
		<div class="head">
			<h1 class="h1">
				{{ organization.title }}
			</h1>

			<NcActions
				:disabled="objectStore.isLoading('organization')"
				:primary="true"
				:menu-name="objectStore.isLoading('organization') ? 'Laden...' : 'Acties'"
				:inline="1"
				title="Acties die je kan uitvoeren op deze publicatie">
				<template #icon>
					<span>
						<NcLoadingIcon v-if="objectStore.isLoading('organization')"
							:size="20"
							appearance="dark" />
						<DotsHorizontal v-if="!objectStore.isLoading('organization')" :size="20" />
					</span>
				</template>
				<NcActionButton
					title="Bekijk de documentatie over organisaties"
					@click="openLink('https://conduction.gitbook.io/opencatalogi-nextcloud/beheerders/organisaties')">
					<template #icon>
						<HelpCircleOutline :size="20" />
					</template>
					Help
				</NcActionButton>
				<NcActionButton @click="navigationStore.setModal('organization')">
					<template #icon>
						<Pencil :size="20" />
					</template>
					Bewerken
				</NcActionButton>
				<NcActionButton @click="navigationStore.setDialog('copyObject', { objectType: 'organization', dialogTitle: 'Glossary'})">
					<template #icon>
						<ContentCopy :size="20" />
					</template>
					Kopiëren
				</NcActionButton>
				<NcActionButton @click="navigationStore.setDialog('deleteObject', { objectType: 'organization', dialogTitle: 'Glossary'})">
					<template #icon>
						<Delete :size="20" />
					</template>
					Verwijderen
				</NcActionButton>
			</NcActions>
		</div>
		<div class="container">
			<div class="detailGrid">
				<div>
					<b>Samenvatting:</b>
					<span>{{ organization.summary || '-' }}</span>
				</div>
				<div>
					<b>Beschrijving:</b>
					<span>{{ organization.description || '-' }}</span>
				</div>
				<div>
					<b>Afbeelding:</b>
					<span>{{ organization.image || '-' }}</span>
				</div>
				<div>
					<b>OIN:</b>
					<span>{{ organization.oin || '-' }}</span>
				</div>
				<div>
					<b>TOOI:</b>
					<span>{{ organization.tooi || '-' }}</span>
				</div>
				<div>
					<b>RSIN:</b>
					<span>{{ organization.rsin || '-' }}</span>
				</div>
				<div>
					<b>PKI:</b>
					<span>{{ organization.pki || '-' }}</span>
				</div>
			</div>
		</div>
	</div>
</template>

<script>
// Components
import { NcActionButton, NcActions, NcLoadingIcon } from '@nextcloud/vue'

// Icons
import ContentCopy from 'vue-material-design-icons/ContentCopy.vue'
import Delete from 'vue-material-design-icons/Delete.vue'
import DotsHorizontal from 'vue-material-design-icons/DotsHorizontal.vue'
import HelpCircleOutline from 'vue-material-design-icons/HelpCircleOutline.vue'
import Pencil from 'vue-material-design-icons/Pencil.vue'

export default {
	name: 'OrganizationDetail',
	components: {
		// Components
		NcLoadingIcon,
		NcActionButton,
		NcActions,
		// Icons
		DotsHorizontal,
		Pencil,
		Delete,
		ContentCopy,
		HelpCircleOutline,
	},
	computed: {
		organization() {
			return objectStore.getActiveObject('organization')
		},
	},
	methods: {
		openLink(url, type = '') {
			window.open(url, type)
		},
	},
}
</script>

<style>
h4 {
  font-weight: bold;
}

.head{
	display: flex;
	justify-content: space-between;
}

.button{
	max-height: 10px;
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

.dataContent {
  display: flex;
  flex-direction: column;
}

.active.organizationDetails-actionsDelete {
    background-color: var(--color-error) !important;
}
.active.organizationDetails-actionsDelete button {
    color: #EBEBEB !important;
}

.OrganizationDetail-clickable {
    cursor: pointer !important;
}

.buttonLinkContainer{
	display: flex;
    align-items: center;
}

.float-right {
    float: right;
}
</style>
