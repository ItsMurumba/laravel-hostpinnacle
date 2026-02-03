import { defineConfig } from 'vitepress'

// https://vitepress.dev/reference/site-config
export default defineConfig({
  title: 'Laravel Hostpinnacle',
  description: 'Official Laravel package for Hostpinnacle SMS API — Quick SMS, Group SMS, File Upload. Single-account and SaaS multi-account support.',
  base: '/',
  // For GitHub Pages project site (user.github.io/laravel-hostpinnacle), use:
  // base: '/laravel-hostpinnacle/',
  vite: {
    server: {
      port: 5174,
    },
  },
  themeConfig: {
    nav: [
      { text: 'Guide', link: '/guide/installation' },
      { text: 'Reference', link: '/reference/config' },
      { text: 'Contributing', link: '/contributing/implementation-outline' },
    ],
    sidebar: [
      {
        text: 'Guide',
        items: [
          { text: 'Installation', link: '/guide/installation' },
          { text: 'Configuration', link: '/guide/configuration' },
          { text: 'Usage', link: '/guide/usage' },
          { text: 'SaaS / Multi-Account', link: '/guide/saas-multi-account' },
          { text: 'Testing', link: '/guide/testing' },
          { text: 'Testing locally (before publish)', link: '/guide/testing-locally-before-publish' },
        ],
      },
      {
        text: 'Reference',
        items: [
          { text: 'Config Options', link: '/reference/config' },
        ],
      },
      {
        text: 'Contributing',
        items: [
          { text: 'SaaS Implementation Outline', link: '/contributing/implementation-outline' },
        ],
      },
    ],
    socialLinks: [
      { icon: 'github', link: 'https://github.com/ItsMurumba/laravel-hostpinnacle' },
      { icon: 'twitter', link: 'https://twitter.com/ItsMurumba' },
    ],
    footer: {
      message: 'Released under the MIT License.',
      copyright: 'Copyright © 2019-present',
    },
  },
})
