# How to obtain these files ?
## Download latest CLDR files
Download link can be found on [CLDR Releases/Downloads](http://cldr.unicode.org/index/downloads) page.

Latest stable release is [CLDR 35.1](http://unicode.org/Public/cldr/35.1/) (released on 2019-04-17).

All needed files can be found in core.zip

## Pick the relevant files

We chose to keep only the files we need :

### core/common/main/

Each file in this folder contain main CLDR data for a given locale code. We kept everything.

### core/common/supplemental/

We kept only :
- **numberingSystems.xml** (for the numbering system / digits mapping)
- **supplementalData.xml** (for currencies and languages additional data and special locales' hierarchy)
