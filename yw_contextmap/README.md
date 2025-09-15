# YW Context Map (TYPO3 Extension)

A frontend plugin that renders the yummy.world context map (OpenSeadragon) and wires in
existing TypoScript libs:

- `lib.ym_viewer_config`          (construct viewer + inline JS)
- `lib.ym_viewer_overlays`        (extra overlays)
- `lib.ym_viewer_menu`            (pie context menu)
- `lib.ym_modalmenu`              (modal menu within context modal)
- `lib.ym_viewer_images`          (language-specific DZI images)
- `lib.ym_viewer_advertisement`   (ads and other overlays)

## Install

1. Copy `yw_contextmap` into your TYPO3 extensions folder or install via Composer.
2. Activate the extension in the TYPO3 backend.
3. Include the static TypoScript "YW Context Map".
4. Add the content element **Plugins → YW Context Map** to a page.

## JS loading

By default the plugin registers core JS via PageRenderer. You can disable this or override paths via FlexForm or TypoScript constants:

```
plugin.tx_ywcontextmap.settings.registerJs = 1
plugin.tx_ywcontextmap.settings.js.ymOsHref = EXT:yw_contextmap/Resources/Public/JavaScript/os/ym-os-href.min.js
plugin.tx_ywcontextmap.settings.js.ymOsBookmark = EXT:yw_contextmap/Resources/Public/JavaScript/os-url/ym-os-bookmark-url.min.js
plugin.tx_ywcontextmap.settings.js.pieCmImages = EXT:yw_contextmap/Resources/Public/JavaScript/pie-context-menu/pie-cm-images.min.js
plugin.tx_ywcontextmap.settings.js.ymOsHtmlOverlay = EXT:yw_contextmap/Resources/Public/JavaScript/os-over/ym_os-html-overlay.min.js
```

If your project already includes these JS files globally, simply uncheck **Register core JS files** in the plugin settings.
