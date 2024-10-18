<script setup>
import { publicationStore } from './../store/store.js'
</script>

<template>
	<!-- Placeholder Div -->
	<div>
		<AddAttachmentModal :drop-files="publicationStore.attachmentFile" />
		<EditAttachmentModal />
		<AddPublicationModal />
		<EditPublicationModal />
		<AddPublicationTypeModal />
		<EditPublicationTypeModal />
		<AddPublicationTypePropertyModal />
		<EditPublicationTypePropertyModal />
		<AddPublicationThemeModal />
		<AddCatalogModal />
		<EditCatalogModal />
		<AddCatalogiPublicationType />
		<AddDirectoryModal />
		<EditListingModal />
		<AddPublicationDataModal />
		<EditPublicationDataModal />
		<AddOrganizationModal />
		<EditOrganizationModal />
		<AddThemeModal />
		<EditThemeModal />
	</div>
</template>

<script>

import AddAttachmentModal from './attachment/AddAttachmentModal.vue'
import EditAttachmentModal from './attachment/EditAttachmentModal.vue'
import AddPublicationTypeModal from './publicationType/AddPublicationTypeModal.vue'
import EditPublicationTypeModal from './publicationType/EditPublicationTypeModal.vue'
import AddPublicationModal from './publication/AddPublicationModal.vue'
import EditPublicationModal from './publication/EditPublicationModal.vue'

import AddPublicationTypePropertyModal from './publicationType/AddPublicationTypePropertyModal.vue'
import EditPublicationTypePropertyModal from './publicationType/EditPublicationTypePropertyModal.vue'
import AddPublicationThemeModal from './publicationTheme/AddPublicationThemeModal.vue'

import AddCatalogModal from './catalog/AddCatalogModal.vue'
import EditCatalogModal from './catalog/EditCatalogModal.vue'
import AddCatalogiPublicationType from './catalogiPublicationType/AddCatalogiPublicationType.vue'
import AddDirectoryModal from './directory/AddDirectoryModal.vue'
import EditListingModal from './directory/EditListingModal.vue'
import AddOrganizationModal from './organization/AddOrganizationModal.vue'
import EditOrganizationModal from './organization/EditOrganizationModal.vue'
import AddPublicationDataModal from './publicationData/AddPublicationDataModal.vue'
import EditPublicationDataModal from './publicationData/EditPublicationDataModal.vue'
import AddThemeModal from './theme/AddThemeModal.vue'
import EditThemeModal from './theme/EditThemeModal.vue'

export default {
	name: 'Modals',
	components: {
		AddAttachmentModal,
		EditAttachmentModal,
		AddPublicationModal,
		EditPublicationModal,
		AddPublicationTypeModal,
		EditPublicationTypeModal,
		AddPublicationTypePropertyModal,
		EditPublicationTypePropertyModal,
		AddPublicationThemeModal,
		AddCatalogModal,
		EditCatalogModal,
		AddCatalogiPublicationType,
		AddDirectoryModal,
		EditListingModal,
		AddPublicationDataModal,
		EditPublicationDataModal,
		AddOrganizationModal,
		EditOrganizationModal,
		AddThemeModal,
		EditThemeModal,
	},
}
</script>
