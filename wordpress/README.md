# Qeema B2B — WordPress + Elementor site

The static design in `../site/` converted to a WordPress site where every section, text, image and
background is editable in Elementor (free version, no Pro needed).

* Site: `http://localhost/qeema-b2b/` (folder `C:\xampp\htdocs\qeema-b2b`, database `qeema_b2b`)
* Admin: `http://localhost/qeema-b2b/wp-admin/` — user `qeema-admin` (password was given when the site was created)
* The old `qeematech-new` site is untouched.

## What is where

| Folder | What | Linked into WordPress as |
|---|---|---|
| `wordpress/qeema-b2b-core/` | Plugin: 22 Elementor widgets, post types, form handler, seeder, all CSS/JS/images | `wp-content/plugins/qeema-b2b-core` (Windows junction) |
| `wordpress/qeema-b2b-theme/` | Minimal theme (no builder of its own): renders the header/footer parts + page content | `wp-content/themes/qeema-b2b` (Windows junction) |
| `tools/build-services.py` | One-off generator used for the static service pages; its copy was exported to `inc/data/services.json` | — |

Code lives in this git repo; WordPress reads it through the junctions, so edits to PHP/CSS/JS show up immediately.

## Editing the site (no code)

* **Pages**: Pages → open a page → *Edit with Elementor*. Every section is a widget in the **Qeema B2B** group
  (left panel). Click a section: *Content* has all texts, images, links, repeaters (cards, rows, logos, …);
  *Style* has the section background (colour / gradient / image), padding, margin and typography + colour of each text.
  Headings and paragraphs can also be edited by clicking them in the preview.
* **Header and footer**: *Header & Footer* in the admin menu → edit the two Site Parts in Elementor.
  (Free Elementor has no Theme Builder, so the newest published part of each type is shown on every page.)
* **Projects** (Our Work grid, Home cards, "more work"): *Projects* → Add New. Title, short description (Excerpt),
  Featured image (the card picture), category, and the box "Project details" (phone/laptop mockup, hero name, tagline).
  The page body is the project's own Elementor content (hero, overview, steps).
* **Services**: six normal pages under *Services* (`/services/<name>/`). The hero artwork has six styles
  (website, mobile, e-commerce, software, branding, marketing); pick one in the *Service Hero* widget.
* **Book a call**: submissions are saved under *Leads*; notification e-mail address: *Leads → Settings*.
  (Local XAMPP cannot send mail until an SMTP plugin/relay is configured.)
* **Page style** box (right side of every page editor) picks the spacing rules of the page wrapper; it is set automatically.

## Editability (what is editable where)

* **Images**: every image control is a Media Library picker with an `alt text` field right below it. If the alt field
  is empty the alt text saved on the Media Library item is used. Default logos and project images are imported into the
  Media Library by the seeder (`wp qb seed`), so they can be swapped like any other file. Many widgets also have an
  *Images* style section (width, corner radius, fit).
* **Project hero**: device frames (`Phone frame` / `Laptop frame`) and the screen image are widget controls; the screen
  image shown is, in order: the widget's own image, the project's *Hero screenshot* (box *Project details*), the
  project's Featured image, then the bundled sample. Button 2 links to the project itself unless you set a link.
* **Project overview**: optional overview image and an optional image/icon per step; texts are generic placeholders.
* **Header and footer**: *Header & Footer* → edit the logos and everything else in Elementor. A notice appears in
  wp-admin (and for editors on the site) if no published header or footer part exists.
* **New projects**: *Projects → Add New* now gets the default layout (hero, overview, more work) automatically on the
  first save, so it is never blank.
* **Site Icon**: set by the seeder from the logo when none exists; change it in *Appearance → Customize → Site Identity*.
* **Book a call form**: *Notification* section of the form widget sets the recipient for that form
  (empty = *Leads → Settings*). The address is stored on the server and never printed in the page (the form only
  carries an opaque token). Validation texts, the two server error messages and the success tick colours are also
  editable in the widget.
* **404 page**: an Elementor page "Page not found" (private, id kept in option `qb_404_page`) is shown for unknown URLs;
  edit it in *Pages*. A plain PHP fallback is used if it does not exist.

## Re-creating / refreshing the demo content

`wp qb seed` (WP-CLI) or *Tools → Qeema B2B setup* rebuilds the pages, service pages, 36 sample projects,
header and footer from the approved design. Pages you edited in Elementor are skipped unless you tick
"overwrite" / pass `--force`.

## Widgets (group "Qeema B2B")

Site Header · Site Footer · Page Background (glow) · Home Hero · Clients Logo Marquee · Services Wheel (scroll) ·
Work Showcase (projects) · Testimonials Slider · Page Head · Icon Cards / Steps · About Hero · Our Story ·
Services Hero (3D) · Service Cards Grid · Service Hero · Service Rows · Deliverables Checklist ·
Previous / Next Service · Work Archive · Project Hero · Project Overview · Book a Call Form

To add a widget: create `widgets/<name>.php` (extend `QB_Widget_Base`) and add one row to `inc/widgets-registration.php`.

## Notes / limits

* Plain-text titles can use `<strong>` for the blue emphasis; in the canvas use the toolbar's Bold.
* Default images are served from the plugin folder; replace any of them from the widget's image control (Media Library).
  Project card images are real Media Library items.
* In the Elementor canvas the pinned Services section is shown as a normal block and the 3D sculpture shows its image
  (so editing stays fast); both run fully on the live page.
* The 3D sculpture uses a local copy of Three.js (`assets/js/vendor/three.min.js`), no CDN.
