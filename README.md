WatermarkFilterBundle
=====================

Sorry for the short documentation, but this code was just meant to be of personal use and show an example on how to create your own custom imagine-filters in Symfony2.

Just install it as every SF2 package by adding it to your composer.js file and run the update-command or downloading the files and adding the package in your AppKernel class.

Configuration
=============

Here's an example on how to use this custom filter:

    liip_imagine:
        filter_sets:
            thumbnail:
                quality: 75
                filters:
                    thumbnail: { size: [120, 80], mode: outbound }
                    watermark:
                        file: %kernel.root_dir%/Resources/misc/watermark.png
                        placement: { position: center, resize: stretch, space: "10%" }
