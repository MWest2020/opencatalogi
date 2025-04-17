<script setup>
import { navigationStore, objectStore } from './../store/store.js'
</script>

<template>
	<!-- Placeholder Div -->
	<div>
		<!--Generic-->
		<CatalogModal />
		<OrganizationModal />
		<ThemeModal />
		<PageModal />
		<GlossaryModal />
		<PublicationModal />
		<MenuModal />
		<!--Specific-->
		<AddAttachmentModal :drop-files="objectStore.getActiveObject('attachment')" />
		<EditAttachmentModal />
		<AddDirectoryModal />
		<EditListingModal v-if="navigationStore.modal === 'editListing'" />
		<AddPageContentsModal v-if="navigationStore.modal === 'addPageContents'" />
		<!-- Menu -->
		<EditMenuItemModal v-if="navigationStore.modal === 'editMenuItem'" />
		<DeleteMenuItemModal v-if="navigationStore.modal === 'deleteMenuItem'" />
		<UploadFilesModal />
	</div>
</template>

<script>

// Generic
import CatalogModal from './catalog/CatalogModal.vue'
import OrganizationModal from './organization/OrganizationModal.vue'
import ThemeModal from './theme/ThemeModal.vue'
import PageModal from './page/PageModal.vue'
import GlossaryModal from './glossary/GlossaryModal.vue'
import UploadFilesModal from './generic/UploadFiles.vue'
// Specific
import AddAttachmentModal from './attachment/AddAttachmentModal.vue'
import EditAttachmentModal from './attachment/EditAttachmentModal.vue'
import PublicationModal from './publication/PublicationModal.vue'

import AddDirectoryModal from './directory/AddDirectoryModal.vue'
import EditListingModal from './directory/EditListingModal.vue'
import AddPageContentsModal from './pageContents/AddPageContents.vue'
// menu
import MenuModal from './menu/MenuModal.vue'
import EditMenuItemModal from './menuItem/EditMenuItemModal.vue'
import DeleteMenuItemModal from './menuItem/DeleteMenuItemModal.vue'

/**
 * Component that contains all modals used in the application
 */
export default {
	name: 'Modals',
	components: {
		AddAttachmentModal,
		EditAttachmentModal,
		PublicationModal,
		CatalogModal,
		AddDirectoryModal,
		EditListingModal,
		OrganizationModal,
		ThemeModal,
		PageModal,
		MenuModal,
		GlossaryModal,
		AddPageContentsModal,
		// menu
		EditMenuItemModal,
		DeleteMenuItemModal,
		UploadFilesModal,
	},
}
</script>
