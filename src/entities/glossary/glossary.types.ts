/**
 * Glossary Types
 * Type definitions for glossary entities
 * @category Entity
 * @package opencatalogi
 * @author Ruben Linde
 * @copyright 2024
 * @license AGPL-3.0-or-later
 * @version 1.0.0
 * @link https://github.com/opencatalogi/opencatalogi
 */

export type TGlossary = {
    id: string
    title: string
    summary: string
    description: string
    externalLink: string
    keywords: string[]
    published: boolean
} 