<?php

/**
 * Breadcrumb Generator
 *
 * Builds and renders a <nav> breadcrumb trail with Schema.org BreadcrumbList
 * structured data embedded as microdata attributes (itemscope/itemprop).
 *
 * Items are added via add_item() and rendered to HTML by render(). The class
 * is instantiated fresh on every call to podnest_breadcrumbs() so there are
 * no stale state issues.
 *
 * @package PodNest
 * @since   1.1.0
 */

defined('ABSPATH') || exit;

/**
 * Class PodNest_Breadcrumbs
 *
 * Usage in templates:
 *   <?php podnest_breadcrumbs(); // via helpers.php ?>
 *
 * Or directly:
 *   <?php ( new PodNest_Breadcrumbs() )->render(); ?>
 */
final class PodNest_Breadcrumbs
{

    /**
     * Ordered list of breadcrumb items.
     *
     * Each item is an array with keys:
     *  - label : Display text (HTML-escaped before output).
     *  - url   : Permalink. Empty string for the current (last) item.
     *
     * @var array<int, array{label: string, url: string}>
     */
    private array $items = [];

    // -- Constructor -----------------------------------------------

    /**
     * Builds the breadcrumb trail for the current page.
     *
     * Home is always the first item. Subsequent items depend on the
     * query context: categories, authors, dates, and singular posts
     * all produce different trails.
     */
    public function __construct()
    {
        $this->build();
    }

    // -- Public API ------------------------------------------------

    /**
     * Outputs the breadcrumb HTML to the page.
     *
     * Wraps items in a <nav> with ARIA label and Schema.org BreadcrumbList.
     * Skips output on the front page (there are no ancestors to show).
     *
     * @return void
     */
    public function render(): void
    {
        if (is_front_page() || empty($this->items)) {
            return;
        }

        echo '<nav class="pn-breadcrumbs" aria-label="' . esc_attr__('Breadcrumb', 'podnest') . '">';
        echo '<ol itemscope itemtype="https://schema.org/BreadcrumbList">';

        foreach ($this->items as $position => $item) {
            echo $this->render_item($item, $position + 1);
        }

        echo '</ol></nav>';
    }

    /**
     * Returns the rendered HTML as a string rather than echoing it.
     *
     * Useful for situations where the breadcrumb needs to be captured
     * in a variable before being placed in the output buffer.
     *
     * @return string Rendered breadcrumb HTML, or empty string on front page.
     */
    public function to_html(): string
    {
        ob_start();
        $this->render();
        return (string) ob_get_clean();
    }

    // -- Builder ---------------------------------------------------

    /**
     * Determines the current page context and populates $this->items.
     *
     * @return void
     */
    private function build(): void
    {
        /* Home is always the first crumb */
        $this->add_item(__('Home', 'podnest'), home_url('/'));

        if (is_singular()) {
            $this->build_singular();
        } elseif (is_category() || is_tag()) {
            $this->add_item((string) single_term_title('', false), '');
        } elseif (is_author()) {
            $this->add_item((string) get_the_author(), '');
        } elseif (is_year()) {
            $this->add_item((string) get_the_date('Y'), '');
        } elseif (is_month()) {
            $this->add_item((string) get_the_date('F Y'), '');
        } elseif (is_search()) {
            $this->add_item(
                sprintf(__('Search: %s', 'podnest'), get_search_query()),
                ''
            );
        } elseif (is_404()) {
            $this->add_item(__('404 — Not Found', 'podnest'), '');
        }
    }

    /**
     * Builds breadcrumb items for singular posts and pages.
     *
     * Posts: Home → Category → Post Title
     * Pages: Home → Parent Page (if any) → Page Title
     * CPTs:  Home → CPT Archive (if registered) → Post Title
     *
     * @return void
     */
    private function build_singular(): void
    {
        global $post;

        if (is_null($post)) {
            return;
        }

        if ('post' === $post->post_type) {
            /* Show the first category as an intermediate crumb */
            $categories = get_the_category($post->ID);
            if (! empty($categories)) {
                $cat = $categories[0];
                $this->add_item($cat->name, (string) get_category_link($cat->term_id));
            }
            $this->add_item(get_the_title(), '');
            return;
        }

        if ('page' === $post->post_type) {
            /* Walk up the parent chain */
            if ($post->post_parent) {
                $ancestors = array_reverse(get_post_ancestors($post->ID));
                foreach ($ancestors as $ancestor_id) {
                    $this->add_item(
                        (string) get_the_title($ancestor_id),
                        (string) get_permalink($ancestor_id)
                    );
                }
            }
            $this->add_item(get_the_title(), '');
            return;
        }

        /* Custom post types */
        if ('podnest_instruction' === $post->post_type) {
            /* No archive — nest under the Support → Usage Instructions pages */
            foreach (['support', 'support/instructions'] as $path) {
                $ancestor = get_page_by_path($path);
                if ($ancestor instanceof WP_Post) {
                    $this->add_item(
                        (string) get_the_title($ancestor),
                        (string) get_permalink($ancestor)
                    );
                }
            }
            $this->add_item(get_the_title(), '');
            return;
        }

        /* Other custom post types — add archive link if one exists */
        $post_type_obj = get_post_type_object($post->post_type);
        if ($post_type_obj && $post_type_obj->has_archive) {
            $archive_url = get_post_type_archive_link($post->post_type);
            if ($archive_url) {
                $this->add_item((string) $post_type_obj->labels->name, $archive_url);
            }
        }
        $this->add_item(get_the_title(), '');
    }

    // -- Item management -------------------------------------------

    /**
     * Appends an item to the breadcrumb trail.
     *
     * @param  string $label Display text for this crumb.
     * @param  string $url   URL to link to. Empty string = current/non-linked item.
     * @return void
     */
    private function add_item(string $label, string $url): void
    {
        $this->items[] = ['label' => $label, 'url' => $url];
    }

    // -- Renderer --------------------------------------------------

    /**
     * Renders a single breadcrumb <li> with Schema.org microdata.
     *
     * Linked items use an <a> tag; the last (current) item uses a <span>
     * to avoid linking a page to itself.
     *
     * @param  array{label: string, url: string} $item     The breadcrumb item data.
     * @param  int                               $position 1-based position for Schema.
     * @return string HTML for this <li>.
     */
    private function render_item(array $item, int $position): string
    {
        $meta_position = '<meta itemprop="position" content="' . esc_attr((string) $position) . '">';

        if (! empty($item['url'])) {
            /* Linked crumb — href is part of the itemprop="item" WebPage */
            $inner = sprintf(
                '<a itemprop="item" href="%s"><span itemprop="name">%s</span></a>%s',
                esc_url($item['url']),
                esc_html($item['label']),
                $meta_position
            );
        } else {
            /* Current page crumb — no link, just the name */
            $inner = sprintf(
                '<span itemprop="name" aria-current="page">%s</span>%s',
                esc_html($item['label']),
                $meta_position
            );
        }

        return '<li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">'
            . $inner
            . '</li>';
    }
}
