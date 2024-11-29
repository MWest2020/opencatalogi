/**
 * Type definition for a Page object
 * Represents the structure of a page with content and metadata
 */
export type TPage = {
    id: string                 // Unique identifier for the page
    uuid: string              // Unique identifier for the page
    name: string             // Title/heading of the page
    contents: Array<any>  // Main content/body of the page - can contain any type of content
    slug: string              // URL-friendly version of the title
    createdAt: string         // Creation timestamp
    updatedAt: string         // Last update timestamp
}
