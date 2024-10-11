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
		<AddMetaDataModal />
		<EditMetaDataModal />
		<AddMetaDataPropertyModal />
		<EditMetaDataPropertyModal />
		<AddCatalogModal />
		<EditCatalogModal />
		<AddCatalogiMetadata />
		<AddDirectoryModal />
		<EditListingModal />
		<AddPublicationDataModal />
		<EditPublicationDataModal />
		<AddorganizationModal />
		<EditOrganisatioModal />
		<AddThemeModal />
		<EditThemeModal />
		<!-- <EditorganizationModal /> -->
	</div>
</template>

<script>

import AddAttachmentModal from './attachment/AddAttachmentModal.vue'
import EditAttachmentModal from './attachment/EditAttachmentModal.vue'
import AddMetaDataModal from './metaData/AddMetaDataModal.vue'
import EditMetaDataModal from './metaData/EditMetaDataModal.vue'
import AddPublicationModal from './publication/AddPublicationModal.vue'
import EditPublicationModal from './publication/EditPublicationModal.vue'

import AddMetaDataPropertyModal from './metaData/AddMetaDataPropertyModal.vue'
import EditMetaDataPropertyModal from './metaData/EditMetaDataPropertyModal.vue'

import AddCatalogModal from './catalog/AddCatalogModal.vue'
import EditCatalogModal from './catalog/EditCatalogModal.vue'
import AddCatalogiMetadata from './catalogiMetadata/AddCatalogiMetadata.vue'
import AddDirectoryModal from './directory/AddDirectoryModal.vue'
import EditListingModal from './directory/EditListingModal.vue'
import AddorganizationModal from './organization/AddorganizationModal.vue'
import EditOrganisatioModal from './organization/EditorganizationModal.vue'
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
		AddMetaDataModal,
		EditMetaDataModal,
		AddMetaDataPropertyModal,
		EditMetaDataPropertyModal,
		AddCatalogModal,
		EditCatalogModal,
		AddCatalogiMetadata,
		AddDirectoryModal,
		EditListingModal,
		AddPublicationDataModal,
		EditPublicationDataModal,
		AddorganizationModal,
		EditOrganisatioModal,
		AddThemeModal,
		EditThemeModal,
		// EditorganizationModal,
	},
}
</script>
