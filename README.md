# AI Post Summary Plugin

Generate concise AI-powered summaries for your WordPress posts with a single shortcode. The plugin sends the first part of your article to OpenAI, caches the summary in post-meta, and outputs it wherever you drop `[ai_summary]`.

---

## Features

* **One-click shortcode** – add `[ai_summary]` under any post and you’re done.
* **OpenAI integration** – uses the `chat/completions` endpoint (`gpt-3.5-turbo` by default).
* **Automatic caching** – summary is stored in post meta (`_ai_summary`) so you only pay for the first request.
* **Safe fallbacks** – clear error messages if the API key is missing or the request fails.
* **Easily extensible** – swap models, tweak prompts, or add a Regenerate button.

---

## Installation

```bash
# Clone the repo into WordPress plugins
cd wp-content/plugins
git clone https://github.com/yourname/ai-summary.git ai-summary

# or download & unzip
```

Activate **AI Post Summary** in **WP Admin → Plugins**.

---

## Set your OpenAI key

Edit `wp-config.php` and add **before** the “That’s all, stop editing!” line:

```php
define( 'OPENAI_API_KEY', 'sk-your-secret-key' );
```

Keep this key private; it is **not** stored in the repo.

---

## Usage

Open any post in the block or classic editor and place the shortcode where you want the summary:

```text
[ai_summary]
```

The first time the post loads, the plugin will:

1. Send up to 3,000 characters of the article to OpenAI.
2. Receive a 2–3-sentence plain-text summary.
3. Cache the result in `post_meta`.
4. Render it inside `<div class="ai-summary-box"> … </div>`.

Subsequent views reuse the cached text—no extra API calls.

---

## Customization

### Change the prompt

Open `ai-summary.php` and modify `$prompt` to adjust tone or length.

### Use a different model

Replace `'gpt-3.5-turbo'` with `'gpt-4o'`, `'gpt-4'`, etc. Make sure your key has access.

### Adjust character limit

Change the `mb_substr($content, 0, 3000)` slice to 5000 or more.

### Regenerate summaries manually

Add a custom meta-box or button that calls `delete_post_meta( $post_id, '_ai_summary' )` then reloads.

---

## Roadmap

* Settings page (choose model & prompt, toggle auto-cache)
* Bulk regeneration tool in WP-Admin
* Support for pages & custom post types
* i18n / translations

---

## License

MIT © 2025 Denys Astapov
