/**
 * API Documentation Page
 * 
 * @category Pages
 * @package Conduction Docs
 * @author Claude AI
 * @copyright 2023 Conduction
 * @license EUPL-1.2
 * @version 1.0.0
 * @link https://conduction.nl
 */

import React from 'react';
import Layout from '@theme/Layout';

export default function ApiPage() {
  return (
    <Layout title="API Documentation">
      <div
        style={{
          display: 'flex',
          justifyContent: 'center',
          alignItems: 'center',
          padding: '2rem',
        }}>
        <div>
          <h1>API Documentation</h1>
          <p>
            The API documentation is currently being updated. Please check back later.
          </p>
          <p>
            You can view the OpenAPI specification directly at{' '}
            <a href="/oas/open-catalogi.yaml" target="_blank" rel="noopener noreferrer">
              /oas/open-catalogi.yaml
            </a>
          </p>
        </div>
      </div>
    </Layout>
  );
} 