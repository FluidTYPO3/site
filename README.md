<img src="https://fluidtypo3.org/logo.svgz" width="100%" />

FluidTYPO3: Site Kickstarter
============================

[![Build Status](https://img.shields.io/travis/FluidTYPO3/site.svg?style=flat-square&label=package)](https://travis-ci.org/FluidTYPO3/site) [![Coverage Status](https://img.shields.io/coveralls/FluidTYPO3/site/development.svg?style=flat-square)](https://coveralls.io/r/FluidTYPO3/site) [![Documentation](http://img.shields.io/badge/documentation-online-blue.svg?style=flat-square)](https://fluidtypo3.org/templating-manual/introduction.html) [![Build Status](https://img.shields.io/travis/FluidTYPO3/fluidtypo3-testing.svg?style=flat-square&label=framework)](https://travis-ci.org/FluidTYPO3/fluidtypo3-testing/) [![Coverage Status](https://img.shields.io/coveralls/FluidTYPO3/fluidtypo3-testing/master.svg?style=flat-square)](https://coveralls.io/r/FluidTYPO3/fluidtypo3-testing)

`EXT:site` is a fire-and-forget install helper which when installed will install every standard FluidTYPO3 dependency - and
when "deployed", creates the pages, mount points, TypoScript and configuration you need to run a FluidTYPO3-based site.

Usage instructions
------------------

Usage is **extremely** simple:

1. Download and install this extension (we recommend you do that from TER).
2. Click the title of the extension to enter the extension configuration.
3. Enter a few key details about how you want your site kickstarted.
4. Save, or save and close, the extension configuration.

After which EXT:site completely destroys itself in order to prevent accidentally generating duplicate setups. To kickstart
another site in the same TYPO3 installation simply re-download EXT:site and perform the steps again (with a new and
different Provider Extension key, of course).
