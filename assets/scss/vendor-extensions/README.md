# Vendor Extensions

If you have to override a section of any vendor, I recommend you have a folder called `vendor-extensions/` in which you may have files named exactly after the vendors they overwrite.

For instance, `vendor-extensions/_bootstrap.scss` is a file containing all CSS rules intended to re-declare some of Bootstrap's default CSS. This is to avoid editing the vendor files themselves, which is generally not a good idea.

Reference: [Sass Guidelines](http://sass-guidelin.es/) > [Architecture](http://sass-guidelin.es/#architecture) > [Vendors folder](http://sass-guidelin.es/#vendors-folder)
