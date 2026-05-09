# wp-podnest

WordPress marketing theme for [PodNest](https://github.com/kpirnie/podnest) — the open-source Podman-based web hosting pod manager.

---

## Requirements

- WordPress 6.3+
- PHP 8.2+
- Node.js 18+ (build only)

---

## Installation

1. Upload the `wp-podnest` folder to `wp-content/themes/`
2. Activate under **Appearance → Themes**
3. Go to **Settings → Reading** and set the homepage to a static page
4. Run the build (see below)

---

## Build

```bash
npm install
npm run build
```

Outputs:
- `assets/css/podnest.css` — minified CSS
- `assets/js/podnest.js` — bundled + minified frontend JS
- `assets/js/editor/podnest-editor.js` — bundled block editor JS

The theme auto-detects the built files and serves them. Falls back to source files if no build has been run.

```bash
npm run build:watch   # rebuild on save during development
npm run build:css     # CSS only
npm run build:js      # JS only
```

---

## Demo Content

Import `podnest-demo-content.xml` via **Tools → Import → WordPress** to populate all four CPTs with the original homepage content.

---

## Content Management

All dynamic homepage sections are managed from **PodNest Content** in wp-admin. No template editing required.

| Section | CPT | Location |
|---|---|---|
| Scrolling marquee strip | Marquee Items | PodNest Content → Marquee |
| Features / Capabilities grid | Features | PodNest Content → Features |
| Runtimes / Site Types grid | Runtimes | PodNest Content → Runtimes |
| Support pricing cards | Pricing Tiers | PodNest Content → Pricing |

### Marquee Items
Title only. Use **Menu Order** (drag in the list view) to control display order.

### Features
- **Title** — card heading
- **Excerpt** — short description shown on the card
- **Content** — full description shown on the Features detail page
- **Icon** (sidebar) — emoji or short text, e.g. `🛡️`
- **Learn More URL** (sidebar) — optional link on the card

### Runtimes
- **Title** — runtime name
- **Excerpt** — short card description
- **Content** — full description shown on the Runtimes detail page
- **Icon** (meta box) — emoji, e.g. `🌐`
- **Versions** (meta box) — one version label per line, rendered as chip badges
- **Learn More URL** (meta box) — optional external docs link

### Pricing Tiers
- **Title** — card heading
- **Content** — short description paragraph
- **Tier Label** — small caps label above the price (e.g. `Hourly`)
- **Price** — numeric amount, no currency symbol
- **Price Unit** — unit label (e.g. `/ hour`)
- **Featured** — highlights the card as recommended
- **Badge Text** — shown on featured cards (e.g. `Most Popular`)
- **Included Features** — one item per line; prefix `x:` to mark as unavailable
- **CTA Button Text / URL**

Use **Menu Order** on all CPTs to control display order.

---

## Gutenberg Blocks

All four CPTs have corresponding dynamic blocks registered under the **PodNest** category in the block inserter. Insert them on any page to render the CPT content outside the homepage.

| Block | Handle |
|---|---|
| Marquee Strip | `podnest/marquee-strip` |
| Features Grid | `podnest/features-grid` |
| Runtimes Grid | `podnest/runtimes-grid` |
| Pricing Table | `podnest/pricing-table` |

---

## Menus

| Location | Purpose |
|---|---|
| Primary Navigation | Top nav bar |
| Social Links | Footer icon row — each item's URL domain maps to an SVG icon |

### Social Links Menu
1. **Appearance → Menus → Create a new menu**
2. Add Custom Links for each platform (GitHub, Discord, X, Facebook)
3. Set the Navigation Label to the platform name — this becomes the `aria-label`
4. Assign to the **Social Links** display location

Supported platforms: `github.com`, `discord.com`, `discord.gg`, `twitter.com`, `x.com`, `facebook.com`. Unknown URLs get a generic link icon.

---

## Customizer

**Appearance → Customize**

| Section | Settings |
|---|---|
| Hero Section | Badge text, headline lines, description, CTA button text + URLs |
| Pricing Defaults | Contact page URL |
| Social Links | GitHub, Discord, X/Twitter, Facebook URLs (fallback when no menu assigned) |
| Footer | Tagline text |

---

## Page Templates

| Template | Purpose |
|---|---|
| `template-features.php` | Full features detail page — lists all Feature posts with expanded content |
| `template-runtimes.php` | Full runtimes detail page — lists all Runtime posts with version details |

Assign these under **Page Attributes → Template** when editing a page. The homepage will automatically link to them when assigned.

---

## Widget Areas

| Sidebar | Location |
|---|---|
| Footer — Product | Footer column 1 |
| Footer — Resources | Footer column 2 |
| Footer — Company | Footer column 3 |
| Blog Sidebar | Blog archive and single post pages |

Footer columns show static fallback links until widgets are assigned.

---

## File Structure

```
wp-podnest/
├── functions.php               Bootstrap — loads classes, boots singleton
├── front-page.php              Marketing homepage
├── header.php / footer.php
├── page.php / single.php / index.php / archive.php / search.php / 404.php
├── template-features.php       Features detail page template
├── template-runtimes.php       Runtimes detail page template
├── build.mjs                   esbuild config
├── package.json
│
├── inc/
│   ├── class-theme.php         Singleton bootstrap — theme support, menus, image sizes
│   ├── class-assets.php        Enqueue CSS/JS, widget sidebars, head cleanup
│   ├── class-seo.php           OG/Twitter meta, JSON-LD structured data
│   ├── class-cpts.php          CPT registration + meta field registration
│   ├── class-meta-boxes.php    Meta box UI, save, admin columns
│   ├── class-blocks.php        Block registration + server-side render callbacks
│   ├── class-customizer.php    Customizer sections and controls
│   ├── class-nav-walker.php    Primary nav walker
│   ├── class-social-walker.php Social icon nav walker
│   ├── class-breadcrumbs.php   Schema.org breadcrumb trail
│   └── helpers.php             Template helper functions
│
└── assets/
    ├── css/
    │   ├── theme.css           Source stylesheet
    │   └── podnest.css         Minified build output (gitignored)
    └── js/
        ├── app.js              Frontend ES module entry point
        ├── modules/            Frontend JS modules (8 files)
        ├── editor/
        │   ├── index.js        Block editor entry point
        │   ├── blocks/         Block definition modules (4 files)
        │   └── utils/
        ├── podnest.js          Minified frontend bundle (gitignored)
        └── editor/
            └── podnest-editor.js  Minified editor bundle (gitignored)
```

---

## SEO

Built-in SEO outputs Open Graph, Twitter Card, canonical, robots meta, and JSON-LD structured data (SoftwareApplication + Organization on the homepage, Article on posts). Automatically suppressed when Yoast, AIOSEO, or RankMath is active.

---

## Nginx

Block access to build tooling files:

```nginx
location ~* ^/wp-content/themes/wp-podnest/(node_modules|package\.json|package-lock\.json|yarn\.lock|build\.mjs) {
    deny all;
    return 403;
}
```

---

## License

See [LICENSE](LICENSE).
