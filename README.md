# FreshMedia

FreshMedia is a MediaWiki skin based on modern design ideas. It aims to provide
a clean experience where blocks are separated by whitespace rather than borders.

This is the README file for Cavendish-MW 0.3.1, a skin for MediaWiki. This skin
is based on Mozilla's Cavendish skin for MediaWiki.

Cavendish-MW 0.3.1 has been tested with MediaWiki 1.21.0. It might not work
well with other versions of MediaWiki.

## Installation Instructions

To install the Cavendish-MW skin, extract the files from the archive to the
"skins" folder of your MediaWiki installation.

You only need to copy the `freshmedia` directory and the `.php` files in  the
root of the repo (`FreshMedia.php` and `FreshMedia.deps.php`)

On your MediaWiki Preferences page, click on the Appearance tab. Under "Skin"
there should be an option "FreshMedia". Select the skin you would like to use
for your account and click Save.

### Set as default

If you want to set the default skin for all users to Cavendish-MW, set the
variable `$wgDefaultSkin` to 'cavendishmw'. The value must be in lowercase,

    $wgDefaultSkin = 'freshmedia';

## License

This work is licensed under the [Creative Commons Attribution-ShareAlike 3.0 Unported License](http://creativecommons.org/licenses/by-sa/3.0/).
