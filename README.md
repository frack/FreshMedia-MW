# FreshMedia

FreshMedia is a MediaWiki skin based on modern design ideas. It aims to provide
a clean experience where blocks are separated by whitespace rather than borders.

This skin is still in development and targets MediaWiki 1.21.

## Installation Instructions

To install the FreshMedia skin, extract the files from the archive to the
"skins" folder of your MediaWiki installation.

Alternatively, you can use Git to clone the entire repository (the advantage
is that you can easily update the skin by running the command `git pull`):

    cd skins/
    git clone https://github.com/frack/FreshMedia-MW.git

You should now have the directory `skins/FreshMedia-MW/` in your MediaWiki
installation.

Now open LocalSettings.php and add the following line somewhere at the end of
the file:

    require_once("$IP/skins/FreshMedia-MW/FreshMedia.php");

That's it.

On your MediaWiki Preferences page, click on the Appearance tab. Under "Skin"
there should be an option "FreshMedia". Select the skin you would like to use
for your account and click Save.

### Set as default

If you want to set the default skin for all users to FreshMedia, set the
variable `$wgDefaultSkin` to 'freshmedia'. The value must be in lowercase,

    $wgDefaultSkin = 'freshmedia';

## License

This work is licensed under the [GNU General Public License, version 2](http://www.gnu.org/licenses/gpl-2.0.html).
