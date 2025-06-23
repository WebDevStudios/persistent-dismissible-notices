[![WebDevStudios. Your Success is Our Mission.](https://webdevstudios.com/wp-content/uploads/2024/02/wds-banner.png)](https://webdevstudios.com/contact/)

# Persistent Dismissible Notices
Persistent Dismissible Notices is a free WordPress® plugin that enhances the handling of admin notices by making them dismissible and ensuring their dismissal persists across sessions.

If you've ever been frustrated by repeatedly dismissing the same notices, this plugin is for you. It stores the dismissal state for logged-in users, improving the user experience and decluttering your admin dashboard.

## Features

- Automatically adds a "dismiss" button to all admin notices.
- Persists dismissal across sessions for notices with IDs.
- Automatically assigns unique IDs to notices without predefined `id` attributes.
- Works seamlessly with custom admin notices.
- Stores dismissed notices as user metadata for scalability and performance.

## Why It's Useful

Admin notices are essential for communicating important information, but they can clutter the WordPress® dashboard when they lack a dismissal option or reappear on every page load.

Persistent Dismissible Notices solves this problem by ensuring:

- **Cleaner dashboards**: Dismissed notices stay dismissed, even after logging out and back in.
- **User-friendly experience**: No more redundant clicks to hide the same messages.
- **Custom notice support**: Works with notices from third-party plugins and themes.

## Installation

1. Download the plugin zip file or clone the repository.
2. Upload the plugin to your WordPress® site:
    - Go to `Plugins > Add New > Upload Plugin`.
    - Select the zip file and click "Install Now."
3. Activate the plugin.

Alternatively, place the plugin folder in the `wp-content/plugins` directory and activate it via the WordPress® admin dashboard.

## How to Use

1. **Default Behavior:** The plugin automatically makes all admin notices dismissible, even those without an `id`attribute. No configuration is required.
2. **Persistent Dismissal:**
    - If a notice has an `id` attribute, dismissing it will persist the dismissal across sessions.
    - For notices without an `id`, the plugin generates a unique `id` based on the notice content, ensuring dismissal persistence.
3. **Custom Notices:** To ensure persistence, add a unique `id` attribute to your custom admin notices.

**Example:**

```
// Notice with ID
add_action('admin_notices', function() {
    echo '<div id="my-custom-notice" class="notice notice-info">This is a custom notice.</div>';
});

// Notice without ID
add_action('admin_notices', function() {
    echo '<div class="notice notice-warning">This is a notice without an ID.</div>';
});
```

* * *

## How It Works

- **JavaScript:** Assigns a unique `id` to notices without one by hashing their content, adds a dismiss button to all admin notices, and handles dismissal via AJAX.
- **AJAX:** Sends the dismissed notice ID (predefined or generated) to the server to store it in the current user's metadata.
- **PHP:** Filters out dismissed notices before they are rendered, ensuring they don't reappear once dismissed.

## Development

### Requirements

- WordPress® 5.0+
- PHP 7.4+

### File Structure

- `persistent-dismissible-notices.php`: Main plugin file.
- `assets/js/dismissible-notices.js`: Handles JavaScript for making notices dismissible.

### Contributing

Contributions are welcome! To contribute:

1. Fork the repository.
2. Create a feature branch (ex: feature/your-feature).
3. Commit your changes and submit a pull request.

## Support

If you encounter any issues or have questions, please open a GitHub issue 🙏

## License

This plugin is licensed under the [GPLv2 or later](https://www.gnu.org/licenses/gpl-2.0.html).

You are free to use, modify, and distribute it under the same terms.

## Acknowledgments

- Special thanks to [Robert DeVore](https://github.com/robertdevore/) for the initial creation and maintenance of this plugin.
- Maintained by [WebDevStudios](https://webdevstudios.com).
